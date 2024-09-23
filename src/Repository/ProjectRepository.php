<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return Project[] Returns an array of Project objects
     */
    public function findAllToArray(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id', 'p.title', 'p.description', 'p.status')
            ->addSelect('COALESCE(SUM(t.estimatedDuration), 0) as duration')
            ->leftJoin('p.tasks', 't')
            ->where('p.deletedAt IS NULL')
            ->andWhere('t.deletedAt IS NULL')
            ->groupBy('p.id')
            ->getQuery()->getResult();
    }

    /**
     * @return array Returns an array of Project objects
     */
    public function findOneByIdToArray(int $id): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 't')
            ->leftJoin('p.tasks', 't')
            ->where('p.id = :id')
            ->andWhere('t.deletedAt IS NULL')
            ->setParameter('id', $id)
            ->getQuery()->getArrayResult();
    }
}
