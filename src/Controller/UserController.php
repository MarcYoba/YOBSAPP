<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/list', name: 'list', methods: ['GET'])]
    public function list(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            // Récupération du rôle principal (en ignorant ROLE_USER par défaut si un autre rôle existe)
            $roles = $user->getRoles();
            $primaryRole = 'Membre';
            foreach ($roles as $r) {
                if ($r !== 'ROLE_USER') {
                    $primaryRole = $this->formatRoleLabel($r);
                    break;
                }
            }

            $data[] = [
                'id' => (string) $user->getId(),
                // Utilise getNom() si défini dans l'entité, sinon extrait le début de l'email
                'nomComplet' => method_exists($user, 'getNom') && $user->getNom() 
                    ? $user->getNom() 
                    : explode('@', $user->getEmail())[0],
                'email' => $user->getEmail(),
                'role' => $primaryRole,
            ];
        }

        return $this->json([
            'success' => true,
            'users' => $data,
        ]);
    }

    private function formatRoleLabel(string $symfonyRole): string
    {
        return match ($symfonyRole) {
            'ROLE_ADMIN' => 'Administrateur',
            'ROLE_PRESIDENT' => 'Président',
            'ROLE_TRESORIER' => 'Trésorier',
            'ROLE_COMMISSAIRE' => 'Commissaire aux Comptes',
            'ROLE_SECRETAIRE' => 'Secrétaire',
            default => 'Membre',
        };
    }

    #[Route('/user/create', name: 'create', methods: ['POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $nomComplet = $data['nomComplet'] ?? null;
        $roleStr = $data['role'] ?? 'Membre';
        $password = $data['password'] ?? 'Pass1234*'; // Mot de passe par défaut si non fourni

        if (!$email) {
            return $this->json(['success' => false, 'message' => 'L\'email est obligatoire.'], 400);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['success' => false, 'message' => 'Cet email est déjà utilisé.'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        
        // Si votre entité possède une propriété/setter pour le nom complet
        if (method_exists($user, 'setNomComplet')) {
            $user->setNomComplet($nomComplet);
        }

        // Mappage du rôle textuel vers les rôles Symfony
        $symfonyRole = $this->mapLabelToSymfonyRole($roleStr);
        $user->setRoles([$symfonyRole, 'ROLE_USER']);

        // Hachage sécurisé du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès.',
            'user' => [
                'id' => (string) $user->getId(),
                'nomComplet' => $nomComplet,
                'email' => $email,
                'role' => $roleStr,
            ]
        ], 201);
    }

    private function mapLabelToSymfonyRole(string $label): string
    {
        return match ($label) {
            'Administrateur' => 'ROLE_ADMIN',
            'Président' => 'ROLE_PRESIDENT',
            'Trésorier' => 'ROLE_TRESORIER',
            'Commissaire aux Comptes' => 'ROLE_COMMISSAIRE',
            'Secrétaire' => 'ROLE_SECRETAIRE',
            default => 'ROLE_USER',
        };
    }

    // recuperation des roles utilisateur connecté
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function getConnectedUser(EntityManagerInterface $em): JsonResponse
    {
        /** @var ?User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $roles = array_values(array_unique($user->getRoles()));

        // Ordre de priorité déterministe pour le rôle principal
        $priorityRoles = [
            'ROLE_ADMIN',
            'ROLE_TRESORIER',
            'ROLE_PRESIDENT',
            'ROLE_SECRETAIRE',
            'ROLE_COMMISSAIRE_AUX_COMPTES',
            'ROLE_MEMBRE',
            'ROLE_USER',
        ];

        $primaryRole = 'ROLE_USER';
        foreach ($priorityRoles as $candidate) {
            if (in_array($candidate, $roles, true)) {
                $primaryRole = $candidate;
                break;
            }
        }
        if ($primaryRole === 'ROLE_USER') {
            foreach ($roles as $r) {
                if ($r !== 'ROLE_USER') {
                    $primaryRole = $r;
                    break;
                }
            }
        }

        // Recherche de la fiche Membre associée (relation directe ou par email)
        $membre = method_exists($user, 'getMembre') ? $user->getMembre() : null;
        if (!$membre instanceof Membre && $user->getEmail()) {
            $membre = $em->getRepository(Membre::class)->findOneBy([
                'email' => strtolower(trim((string) $user->getEmail())),
            ]);
        }

        $hasMembre = $membre instanceof Membre;
        $isAdmin = in_array('ROLE_ADMIN', $roles, true);
        $forbiddenAdminMembreLink = $isAdmin && $hasMembre;

        $nomComplet = method_exists($user, 'getNomComplet') && $user->getNomComplet()
            ? (string) $user->getNomComplet()
            : ($hasMembre ? trim($membre->getPrenom() . ' ' . $membre->getNom()) : '');

        return $this->json([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'nomComplet' => $nomComplet,
                'role' => $primaryRole,
                'roles' => $roles,
                'hasMembre' => $hasMembre,
                'membreId' => $hasMembre ? $membre->getId() : null,
                'forbiddenAdminMembreLink' => $forbiddenAdminMembreLink,
            ],
        ]);
    
    }
}
