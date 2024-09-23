<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route('/api/projects', name: 'get_projects', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): JsonResponse
    {
        $code = 0;
        $error = null;
        $data = [];

        try {
            $data = $projectRepository->findAllToArray();
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

    #[Route('/api/projects/{id}', name: 'get_project_by_id', methods: ['GET'])]
    public function getProjectAction(int $id, ProjectRepository $projectRepository): JsonResponse
    {
        $code = 0;
        $error = null;
        $project = [];

        try {
            $project = $projectRepository->findOneByIdToArray($id);
        } catch (\Exception $exception) {
            $code = -1;
            $error = $exception->getMessage();
        }

        return $this->json([
            'code' => $code,
            'data' => $project,
            'validation_errors' => $error
        ]);
    }

    /**
     * When using an api doc:
     * Property(
     *  name: "title",
     *  required: true,
     *  type: string
     *  description: "Title of the project"
     * ),
     * Property(
     *  name: "description",
     *  required: true,
     *  type: string
     *  description: "Description of the project"
     * ),
     * Property(
     *  name: "status",
     *  required: false,
     *  type: string
     *  description: "Current status of the project"
     * )
     */
    #[Route('/api/projects', name: 'new_project', methods: ['POST'])]
    public function newProjectAction(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $code = 0;
        $error = null;

        try {
            $project = (new Project())
                ->setTitle($data['title'])
                ->setDescription($data['description']);

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

    /**
     * When using an api doc:
     * Property(
     *  name: "title",
     *  required: false,
     *  type: string
     *  description: "Title of the project"
     * ),
     * Property(
     *  name: "description",
     *  required: false,
     *  type: string
     *  description: "Description of the project"
     * ),
     * Property(
     *  name: "status",
     *  required: false,
     *  type: string
     *  description: "Current status of the project"
     * )
     */
    #[Route('/api/projects/{id}', name: 'update_project', methods: ['PATCH'])]
    public function updateProjectAction(
        int $id,
        Request $request,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $code = 0;
        $error = null;
        $data = json_decode($request->getContent(), true);

        try {
            $project = $projectRepository->find($id);
            $methods = get_class_methods($project);
            foreach ($data as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (in_array($method, $methods)) {
                    $project->$method($value);
                } else {
                    $code = -1;
                    $error = 'Invalid method name';
                }
            }

            $project->setUpdatedAt(new DateTime());
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

    #[Route('/api/projects/{id}', name: 'delete_project', methods: ['DELETE'])]
    public function deleteProjectAction(
        int $id,
        ProjectRepository $projectRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $code = 0;
        $error = null;

        try {
            $project = $projectRepository->find($id);
            $project->setDeletedAt(new DateTime());
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
}
