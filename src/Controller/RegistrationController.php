<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'register', methods: ['POST'])]
    public function registerAction(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $user = new User();
        $userData = json_decode($request->getContent(), true);
        $message = 'User created';

        try {
            $user
                ->setUsername($userData['username'])
                ->setPassword(
                    $userPasswordHasher->hashPassword($user, $userData['password']));

            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }

        return $this->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
