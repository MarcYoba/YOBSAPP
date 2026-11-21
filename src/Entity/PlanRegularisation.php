<?php

namespace App\Entity;

use App\Repository\PlanRegularisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanRegularisationRepository::class)]
class PlanRegularisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $MontantDuFige = 0.0;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateAdhesion = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $delaiMois = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateLimite = null;

    #[ORM\Column]
    private ?float $montantVerse = 0.0;

    #[ORM\Column]
    private ?float $reliquat = 0.0;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = 'inactif';

    #[ORM\ManyToOne(inversedBy: 'planRegularisations')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'plansRegularisation')]
    private ?Membre $membre = null;

    /**
     * @var Collection<int, EcheanceRegul>
     */
    #[ORM\OneToMany(targetEntity: EcheanceRegul::class, mappedBy: 'plan')]
    private Collection $echeanceReguls;

    #[ORM\ManyToOne(inversedBy: 'planRegularisations')]
    private ?Fonds $fonds = null;

    public function __construct()
    {
        $this->echeanceReguls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantDuFige(): ?float
    {
        return $this->MontantDuFige;
    }

    public function setMontantDuFige(float $MontantDuFige): static
    {
        $this->MontantDuFige = $MontantDuFige;

        return $this;
    }

    public function getDateAdhesion(): ?\DateTime
    {
        return $this->dateAdhesion;
    }

    public function setDateAdhesion(?\DateTimeInterface $dateAdhesion): static
    {
        $this->dateAdhesion = $dateAdhesion instanceof \DateTimeImmutable ? 
        \DateTime::createFromImmutable($dateAdhesion) : $dateAdhesion;

        return $this;
    }

    public function getDelaiMois(): ?string
    {
        return $this->delaiMois;
    }

    public function setDelaiMois(?string $delaiMois): static
    {
        $this->delaiMois = $delaiMois;

        return $this;
    }

    public function getDateLimite(): ?\DateTime
    {
        return $this->dateLimite;
    }

    public function setDateLimite(?\DateTimeInterface $dateLimite): static
    {
        $this->dateLimite = $dateLimite instanceof \DateTimeImmutable ? 
        \DateTime::createFromImmutable($dateLimite) : $dateLimite;

        return $this;
    }

    public function getMontantVerse(): ?float
    {
        return $this->montantVerse;
    }

    public function setMontantVerse(float $montantVerse): static
    {
        $this->montantVerse = $montantVerse;

        return $this;
    }

    public function getReliquat(): ?float
    {
        return $this->reliquat;
    }

    public function setReliquat(float $reliquat): static
    {
        $this->reliquat = $reliquat;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): static
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * @return Collection<int, EcheanceRegul>
     */
    public function getEcheanceReguls(): Collection
    {
        return $this->echeanceReguls;
    }

    public function addEcheanceRegul(EcheanceRegul $echeanceRegul): static
    {
        if (!$this->echeanceReguls->contains($echeanceRegul)) {
            $this->echeanceReguls->add($echeanceRegul);
            $echeanceRegul->setPlan($this);
        }

        return $this;
    }

    public function removeEcheanceRegul(EcheanceRegul $echeanceRegul): static
    {
        if ($this->echeanceReguls->removeElement($echeanceRegul)) {
            // set the owning side to null (unless already changed)
            if ($echeanceRegul->getPlan() === $this) {
                $echeanceRegul->setPlan(null);
            }
        }

        return $this;
    }

    public function getFonds(): ?Fonds
    {
        return $this->fonds;
    }

    public function setFonds(?Fonds $fonds): static
    {
        $this->fonds = $fonds;

        return $this;
    }
}
