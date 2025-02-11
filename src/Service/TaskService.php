<?php

namespace App\Service;

use App\Dto\FetchResultDto;
use App\Dto\TaskDto;
use App\Dto\TaskFilterDto;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\StatusType;
use App\Exception\ValidationException;
use App\Helper\ValidationHelper;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TaskService
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;
    private ValidationHelper $validationHelper;


    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository,
                                ValidationHelper       $validator)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->validationHelper = $validator;
    }

    public function getTask(int $id): task
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if (!$task) {
            throw new ValidationException('Task not found.', 404);
        }
        return $task;
    }

    public function listTasks(TaskFilterDto  $filter,
                              FetchResultDto $fetchResultDto,
    ): FetchResultDto
    {
        $queryBuilder = $this->taskRepository->findByFilters($filter);


        $queryBuilder->setFirstResult(($fetchResultDto->page - 1) * $fetchResultDto->limit)
            ->setMaxResults($fetchResultDto->limit);

        $paginator = new Paginator($queryBuilder);


        $tasks = iterator_to_array($paginator);

        $fetchResultDto->setResult($tasks ?: []);
        $fetchResultDto->setTotal(count($paginator) ?: 0);
        return $fetchResultDto;
    }

    public function upsertTask(TaskDto $dto, ?User $currentUser): Task
    {
        if ($dto->id == null) {//create
            $task = new Task();

            $task->setCreatedAt(new DateTime());
            $task->setCreatedBy($currentUser);
            $task->setStatus($dto->status ?: StatusType::OPEN->value);
        } else {//edit
            $task = $this->getTask($dto->id);

            $task->setUpdatedAt(new DateTime());
            $task->setUpdatedBy($currentUser);
            if ($dto->status !== null) {
                $task->setStatus($dto->status);
            }
        }

        if ($dto->title !== null) {
            $task->setTitle($dto->title);
        }

        if ($dto->description !== null) {
            $task->setDescription($dto->description);
        }
        if ($dto->priority !== null) {
            $task->setPriority($dto->priority);
        }

        if ($dto->dueDate !== null) {
            $task->setDueDate($dto->dueDate);
        }

        if ($dto->assigneeId != null) {
            $assignee = $this->entityManager->getRepository(User::class)->find($dto->assigneeId);
            if (!$assignee) {
                throw new ValidationException('Assignee not found.', 404);
            }
            $task->setAssignee($assignee);
        }
        if ($dto->parentId != null) {
            $parent = $this->entityManager->getRepository(Task::class)->find($dto->parentId);
            if (!$parent) {
                throw new ValidationException('Parent not found.', 404);
            }
            $task->setParent($parent);
        }

        $this->validationHelper->validate($task);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function getTaskStatistics(int $userId): array
    {
        return $this->taskRepository->countTasksByStatus($userId);
    }

}
