<?php

namespace App\Entity;

use App\Repository\EcheanceRegulRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcheanceRegulRepository::class)]
class EcheanceRegul
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $rang = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateReglement = null;

    #[ORM\ManyToOne(inversedBy: 'echeanceReguls')]
    private ?PlanRegularisation $plan = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRang(): ?string
    {
        return $this->rang;
    }

    public function setRang(string $rang): static
    {
        $this->rang = $rang;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

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

    public function getDateReglement(): ?\DateTime
    {
        return $this->dateReglement;
    }

    public function setDateReglement(\DateTimeInterface $dateReglement): static
    {
        $this->dateReglement = $dateReglement instanceof \DateTimeImmutable ? 
        \DateTime::createFromImmutable($dateReglement) : $dateReglement;

        return $this;
    }

    public function getPlan(): ?PlanRegularisation
    {
        return $this->plan;
    }

    public function setPlan(?PlanRegularisation $plan): static
    {
        $this->plan = $plan;

        return $this;
    }
}
