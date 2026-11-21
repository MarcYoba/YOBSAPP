<?php

namespace App\Controller;

use App\Entity\AdhesionFonds;
use App\Entity\EcheanceRegul;
use App\Entity\Membre;
use App\Entity\PlanRegularisation;
use App\Entity\User;
use App\Repository\FondsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class MembreController extends AbstractController
{
    #[Route('/api/membre', name: 'app_api_membre', methods: ['POST'])]
    //#[IsGranted('ROLE_TRESORIER')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        FondsRepository $fondsRepository,
        UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json([
                'success' => false,
                'message' => 'Payload JSON invalide.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $nom = trim((string) ($data['nom'] ?? ''));
        $prenom = trim((string) ($data['prenom'] ?? ''));
        $telephone = trim((string) ($data['telephone'] ?? ''));
        $pieceNumero = trim((string) ($data['pieceNumero'] ?? ''));
        $dateEntreeRaw = $data['dateEntree'] ?? null;

        if ($nom === '' || $prenom === '' || $telephone === '' || $pieceNumero === '' || !$dateEntreeRaw) {
            return $this->json([
                'success' => false,
                'message' => 'Les champs nom, prénom, téléphone, pièce et date d\'entrée sont requis.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dateEntree = new \DateTimeImmutable((string) $dateEntreeRaw);
        } catch (\Exception) {
            return $this->json([
                'success' => false,
                'message' => 'Format de dateEntree invalide.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = isset($data['email']) && trim((string) $data['email']) !== ''
            ? strtolower(trim((string) $data['email']))
            : null;
            
        if ($email !== null) {
            /** @var ?User $existingUser */
            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser instanceof User) {
                if (in_array('ROLE_ADMIN', $existingUser->getRoles(), true)) {
                    throw new \DomainException(
                        'Un compte Administrateur/Développeur ne peut pas être rattaché à un membre.'
                    );
                }
                throw new \DomainException(
                    'Un compte utilisateur existe déjà avec cet e-mail.'
                );
            }

            /** @var ?Membre $existingMembre */
            $existingMembre = $em->getRepository(Membre::class)->findOneBy(['email' => $email]);
            if ($existingMembre instanceof Membre) {
                throw new \DomainException(
                    'Une fiche membre existe déjà avec cet e-mail.'
                );
            }
        }

        $indivision = (bool) ($data['indivision'] ?? false);
        $porteurIndex = $data['porteurIndex'] ?? null;
        if ($indivision && $porteurIndex === null) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez désigner le porteur de la part indivise.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $result = $em->wrapInTransaction(function () use (
                $em,
                $fondsRepository,
                $passwordHasher,
                $data,
                $nom,
                $prenom,
                $telephone,
                $email,
                $pieceNumero,
                $dateEntree
            ): array {
                $membre = new Membre();
                $membre->setNom($nom);
                $membre->setPrenom($prenom);
                $membre->setTelephone($telephone);
                $membre->setEmail($email);
                $membre->setAdresse(isset($data['adresse']) ? trim((string) $data['adresse']) : null);
                $membre->setPieceType((string) ($data['pieceType'] ?? 'CNI'));
                $membre->setPieceNumero($pieceNumero);
                $membre->setDateEntree($dateEntree);
                $membre->setStatut('Candidat');
                $membre->setPersonnePrevenirNom(isset($data['personnePrevenir']['nom']) ? trim((string) $data['personnePrevenir']['nom']) : null);
                $membre->setPersonnePrevenirLien(isset($data['personnePrevenir']['lien']) ? trim((string) $data['personnePrevenir']['lien']) : null);
                $membre->setPersonnePrevenirTel(isset($data['personnePrevenir']['telephone']) ? trim((string) $data['personnePrevenir']['telephone']) : null);

                $createdUser = null;
                $temporaryPassword = null;

                if ($email !== null) {
                    $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                    if ($existingUser instanceof User) {
                        throw new \DomainException('Un compte utilisateur existe déjà avec cet email.');
                    }

                    $user = new User();
                    $user->setEmail($email);
                    $user->setNomComplet(trim($prenom . ' ' . $nom));
                    $user->setRoles(['ROLE_MEMBRE']);
                    $temporaryPassword = bin2hex(random_bytes(6));
                    $user->setPassword($passwordHasher->hashPassword($user, $temporaryPassword));

                    $em->persist($user);
                    $membre->setUtilisateur($user);
                    $createdUser = $user;
                }

                $em->persist($membre);

                // 3. Fonds de solidarité (obligatoire)
                $fsData = $data['fondsSolidarite'] ?? [];
                $montantDuFs = (float) ($fsData['montantDu'] ?? 87500);
                $modeReglementFs = (string) ($fsData['modeReglement'] ?? 'comptant');

                $fondsFs = $fondsRepository->findOneBy(['code' => 'FS']);
                if ($fondsFs !== null) {
                    $adhesionFs = new AdhesionFonds();
                    $adhesionFs->setMembre($membre);
                    $adhesionFs->setFonds($fondsFs);
                    $adhesionFs->setMontantDu($montantDuFs);
                    $adhesionFs->setMontantVerse($modeReglementFs === 'comptant' ? $montantDuFs : 0.0);
                    $adhesionFs->setStatut($modeReglementFs === 'comptant' ? 'A_JOUR' : 'EN_COURS_REGULARISATION');
                    $em->persist($adhesionFs);

                    if ($modeReglementFs === 'regularisation') {
                        $delaiMois = max(1, (int) ($fsData['delaiMois'] ?? 3));
                        $nbEcheances = max(1, (int) ($fsData['nbEcheances'] ?? 3));
                        $montantEcheance = round($montantDuFs / $nbEcheances, 2);

                        $plan = new PlanRegularisation();
                        $plan->setMembre($membre);
                        $plan->setFonds($fondsFs);
                        $plan->setMontantDuFige($montantDuFs);
                        $plan->setDateAdhesion($dateEntree);
                        $plan->setDelaiMois($delaiMois);
                        $plan->setDateLimite($dateEntree);
                        $plan->setMontantVerse(0.0);
                        $plan->setReliquat($montantDuFs);
                        $plan->setStatut('EN_COURS');
                        $em->persist($plan);

                        for ($rang = 1; $rang <= $nbEcheances; $rang++) {
                            $echeance = new EcheanceRegul();
                            $echeance->setPlan($plan);
                            $echeance->setRang($rang);
                            $echeance->setDateReglement($dateEntree);
                            $echeance->setMontant($montantEcheance);
                            $echeance->setStatut('A_REGLER');
                            $em->persist($echeance);
                        }
                    }
                }

                // 4. Fonds d'investissement (optionnel)
                $fiData = $data['fondsInvestissement'] ?? [];
                if (!empty($fiData['adhesion'])) {
                    $fondsFi = $fondsRepository->findOneBy(['code' => 'FI']);
                    if ($fondsFi !== null) {
                        $adhesionFi = new AdhesionFonds();
                        $adhesionFi->setMembre($membre);
                        //$adhesionFi->setFonds($fondsFi);
                        $adhesionFi->setMontantDu((float) ($fiData['montantDu'] ?? 0));
                        $adhesionFi->setMontantVerse(0.0);
                        $adhesionFi->setMotifDerogation(
                            isset($fiData['motifDerogation']) && trim((string) $fiData['motifDerogation']) !== ''
                                ? trim((string) $fiData['motifDerogation'])
                                : null
                        );
                        $adhesionFi->setStatut('EN_ATTENTE_VALIDATION');
                        $em->persist($adhesionFi);
                    }
                }

                $em->flush();

                return [
                    'membre' => $membre,
                    'user' => $createdUser,
                    'temporaryPassword' => $temporaryPassword,
                ];
            });
        } catch (\DomainException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_CONFLICT);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erreur lors de la création du membre.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        /** @var Membre $membre */
        $membre = $result['membre'];
        /** @var ?User $user */
        $user = $result['user'];

        return $this->json([
            'success' => true,
            'message' => 'Fiche membre créée avec succès.',
            'membre' => [
                'id' => $membre->getId(),
                'nom' => $membre->getNom(),
                'prenom' => $membre->getPrenom(),
                'statut' => $membre->getStatut(),
            ],
            'compteUtilisateur' => $user ? [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'nomComplet' => $user->getNomComplet(),
                'role' => 'ROLE_MEMBRE',
                'motDePasseTemporaire' => $result['temporaryPassword'],
            ] : null,
        ], JsonResponse::HTTP_CREATED);
    
    }
}
