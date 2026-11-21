<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api', name: 'api_')]
class RegisterAdminController extends AbstractController
{
    #[Route('/register-admin', name: 'register_admin')]
    public function register(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        // 1. Sécurité : On vérifie à nouveau s'il n'y a pas DÉJÀ un utilisateur (pour éviter qu'on recrée un admin par surprise)
        if ($userRepository->count([]) > 0) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Un administrateur existe déjà.'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        // 2. Récupérer les données JSON envoyées par Flutter
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Email ou mot de passe manquant.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // 3. Créer le nouvel utilisateur
        $user = new User();
        $user->setEmail($email);
        
        // Attribuer le rôle Administrateur
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsActive(true);

        // Hacher le mot de passe de façon sécurisée
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // 4. Sauvegarder en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Administrateur créé avec succès !'
        ], JsonResponse::HTTP_CREATED);
    }
}