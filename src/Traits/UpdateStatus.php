<?php

namespace App\Traits;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

trait UpdateStatus
{
    protected EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateStatus(Project $project): void
    {
        $completed = false;
        foreach ($project->getTasks() as $task) {
            if ('COMPLETED' !== $task->getStatus()) {
                continue;
            }

            $completed = true;
        }

        if ($completed) {
            $project->setStatus('COMPLETED');
            $this->entityManager->persist($project);
        }
    }
}
