<?php

namespace App\Repository;

use App\Dto\TaskFilterDto;
use App\Entity\Task;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function countTasksByStatus(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.status, COUNT(t.id) as taskCount')
            ->where('t.assignee = :userId')
            ->setParameter('userId', $userId)
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(TaskFilterDto $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        if (!empty($filters->status)) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $filters->status);
        }

        if (!empty($filters->createdFrom)) {
            $qb->andWhere('t.createdAt >= :createdFrom')
                ->setParameter('createdFrom', new DateTime($filters->createdFrom));
        }

        if (!empty($filters->createdTo)) {
            $qb->andWhere('t.createdAt <= :createdTo')
                ->setParameter('createdTo', new DateTime($filters->createdTo));
        }

        if (!empty($filters->assigneeId)) {
            $qb->join('t.assignee', 'u')
                ->andWhere('u.id = :id')
                ->setParameter('id', $filters->assigneeId);
        }

        return $qb;
    }

}
