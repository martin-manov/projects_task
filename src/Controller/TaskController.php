<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Traits\UpdateStatus;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    use UpdateStatus;

    /**
     * When using an api doc:
     * Property(
     *  name: "title",
     *  required: true,
     *  type: string
     *  description: "Title of the task"
     * ),
     * Property(
     *  name: "estimatedDuration",
     *  required: true,
     *  type: integer
     *  description: "Duration of the task in minutes"
     * ),
     * Property(
     *  name: "description",
     *  required: true,
     *  type: string
     *  description: "Description of the task"
     * ),
     * Property(
     *  name: "projectId",
     *  required: true,
     *  type: integer
     *  description: "ID of the project for the task"
     * ),
     *  Property(
     *   name: "status",
     *   required: false,
     *   type: string
     *   description: "Current status of the task"
     *  )
     */
    #[Route('/api/tasks', name: 'new_task', methods: ['POST'])]
    public function newTaskAction(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $code = 0;
        $error = null;

        try {
            $project = $entityManager->getRepository(Project::class)->find($data['projectId']);
            $task = (new Task())
                ->setTitle($data['title'])
                ->setEstimatedDuration($data['estimatedDuration'])
                ->setDescription($data['description'])
                ->setProject($project);

            $entityManager->persist($task);

            $project->setStatus('IN_PROGRESS');
            $entityManager->persist($project);

            $entityManager->flush();
        } catch (\Exception $exception) {
            $code = -1;
            $error = $exception->getMessage();
        }

        return $this->json([
            'code' => $code,
            'validation_errors' => $error
        ]);
    }

    #[Route('/api/tasks/{id}', name: 'get_task_by_id', methods: ['GET'])]
    public function getTaskAction(
        int $id,
        TaskRepository $taskRepository
    ): JsonResponse {
        $code = 0;
        $data = null;
        $error = null;

        try {
            $data = $taskRepository->findOneByIdToArray($id);
        } catch (\Exception $exception) {
            $code = -1;
            $error = $exception->getMessage();
        }

        return $this->json([
            'code' => $code,
            'data' => $data,
            'validation_errors' => $error
        ]);
    }

    /**
     * When using an api doc:
     * Property(
     *  name: "title",
     *  required: false,
     *  type: string
     *  description: "Title of the task"
     * ),
     * Property(
     *  name: "estimatedDuration",
     *  required: false,
     *  type: integer
     *  description: "Duration of the task in minutes"
     * ),
     * Property(
     *  name: "description",
     *  required: false,
     *  type: string
     *  description: "Description of the task"
     * ),
     *  Property(
     *   name: "status",
     *   required: false,
     *   type: string
     *   description: "Current status of the task"
     *  )
     */
    #[Route('/api/tasks/{id}', name: 'update_task', methods: ['PATCH'])]
    public function updateTaskAction(
        int $id,
        Request $request,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $code = 0;
        $error = null;
        $data = json_decode($request->getContent(), true);

        try {
            $task = $taskRepository->find($id);
            $project = $task->getProject();
            $methods = get_class_methods($task);
            foreach ($data as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (in_array($method, $methods)) {
                    $task->$method($value);
                } else {
                    $code = -1;
                    $error = 'Invalid method name';
                }
            }

            $task->setUpdatedAt(new DateTime());
            $entityManager->persist($task);

            $this->updateStatus($project);

            $entityManager->flush();
        } catch (\Exception $exception) {
            $code = -1;
            $error = $exception->getMessage();
        }

        return $this->json([
            'code' => $code,
            'validation_errors' => $error
        ]);
    }

    #[Route('/api/tasks/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTaskAction(
        int $id,
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $code = 0;
        $error = null;

        try {
            $task = $taskRepository->find($id);
            $task->setDeletedAt(new DateTime());
            $entityManager->persist($task);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $code = -1;
            $error = $exception->getMessage();
        }

        return $this->json([
            'code' => $code,
            'validation_errors' => $error
        ]);
    }
}
