<?php

namespace App\Controller;

use App\Dto\FetchResultDto;
use App\Dto\TaskDto;
use App\Dto\TaskFilterDto;
use App\Entity\User;
use App\Helper\ValidationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\TaskService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api/tasks')]
#[Security(name: "BearerAuth")]
final class TaskController extends AbstractController
{

    private TaskService $taskService;
    private ValidationHelper $validationHelper;
    private EntityManagerInterface $entityManager;


    public function __construct(TaskService $taskService, ValidationHelper $validationHelper, EntityManagerInterface $entityManager)
    {
        $this->taskService = $taskService;
        $this->validationHelper = $validationHelper;
        $this->entityManager = $entityManager;
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getTask(int $id): JsonResponse
    {
        $task = $this->taskService->getTask($id);
        return new JsonResponse($task->dtoEntity(), Response::HTTP_OK);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        parameters: [
            new OA\Parameter(
                name: "filters",
                in: "query",
                required: false,
                schema: new OA\Schema(ref: new Model(type: TaskFilterDto::class))
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10)
            )
        ])]
    public function listTasks(Request $request): JsonResponse
    {
        $filterDTO = TaskFilterDTO::fromRequest($request);
        $fetchResult = FetchResultDto::fromRequest($request);

        $this->validationHelper->validate($filterDTO);

        $fetchResult = $this->taskService->listTasks($filterDTO, $fetchResult);
        $taskDTOs = array_map(fn($task) => $task->dtoEntity(), $fetchResult->getResult());
        $fetchResult->setResult($taskDTOs);
        return new JsonResponse($fetchResult, Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OA\RequestBody(content: new Model(type: TaskDto::class))]
    public function createTask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $taskDTO = TaskDTO::fromArray($data);

        $result = $this->taskService->upsertTask($taskDTO, $this->getCurrentUser());
        return new JsonResponse($result->dtoEntity(), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OA\RequestBody(content: new Model(type: TaskDto::class))]
    public function updateTask(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $taskDTO = TaskDTO::fromArray($data);

        $taskDTO->id = $id;

        $result = $this->taskService->upsertTask($taskDTO, $this->getCurrentUser());
        return new JsonResponse($result->dtoEntity(), Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteTask(int $id): JsonResponse
    {
        $task = $this->taskService->getTask($id);
        $this->taskService->deleteTask($task);
        return new JsonResponse(['message' => 'Task deleted successfully'], Response::HTTP_OK);

    }

    #[Route('/stat', methods: ['GET'], priority: 2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function taskStat(): JsonResponse
    {
        $result = $this->taskService->getTaskStatistics($this->getUser()->getId());
        return new JsonResponse($result, Response::HTTP_OK);
    }

//----------------------------------
    public function getCurrentUser(): ?User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        }

        return $user;
    }
}
