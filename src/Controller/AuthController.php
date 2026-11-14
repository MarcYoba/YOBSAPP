<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    #[Route('/auth/check-users', name: 'app_auth')]
    public function index(UserRepository $userRepository): Response
    {
        $userCount = $userRepository->count([]);

        return new JsonResponse([
            'has_users' => $userCount > 0
        ]);
    }
}
