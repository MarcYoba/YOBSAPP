<?php

namespace App\Entity;

use App\Repository\AdhesionFondsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdhesionFondsRepository::class)]
class AdhesionFonds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montantDu = 0.0;

    #[ORM\Column]
    private ?float $montantVerse = 0.0;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateMiseAjour = null;

    #[ORM\Column(length: 100)]
    private ?string $statut = 'inactif';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifDerogation = 'inconnu';

    #[ORM\ManyToOne(inversedBy: 'adhesionFonds')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'adhesionsFonds')]
    private ?Membre $membre = null;

    #[ORM\ManyToOne(inversedBy: 'adhesionFonds')]
    private ?Fonds $fonds = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantDu(): ?float
    {
        return $this->montantDu;
    }

    public function setMontantDu(float $montantDu): static
    {
        $this->montantDu = $montantDu;

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

    public function getDateMiseAjour(): ?\DateTime
    {
        return $this->dateMiseAjour;
    }

    public function setDateMiseAjour(\DateTime $dateMiseAjour): static
    {
        $this->dateMiseAjour = $dateMiseAjour;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMotifDerogation(): ?string
    {
        return $this->motifDerogation;
    }

    public function setMotifDerogation(?string $motifDerogation): static
    {
        $this->motifDerogation = $motifDerogation;

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
