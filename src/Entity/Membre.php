<?php

namespace App\Entity;

use App\Repository\MembreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100)]
    private ?string $telephone = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 50)]
    private ?string $pieceType = 'CNI';

    #[ORM\Column(length: 100)]
    private ?string $pieceNumero = '0';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateEntree = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateSaisie = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = 'Candidat';

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $personnePrevenirNom = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $personnePrevenirLien = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $personnePrevenirTel = null;

    #[ORM\OneToOne(inversedBy: 'membre', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'membres')]
    private ?User $creePart = null;

    /**
     * @var Collection<int, AdhesionFonds>
     */
    #[ORM\OneToMany(targetEntity: AdhesionFonds::class, mappedBy: 'membre')]
    private Collection $adhesionsFonds;

    /**
     * @var Collection<int, PlanRegularisation>
     */
    #[ORM\OneToMany(targetEntity: PlanRegularisation::class, mappedBy: 'membre')]
    private Collection $plansRegularisation;

    public function __construct()
    {
        $this->adhesionsFonds = new ArrayCollection();
        $this->plansRegularisation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getPieceType(): ?string
    {
        return $this->pieceType;
    }

    public function setPieceType(string $pieceType): static
    {
        $this->pieceType = $pieceType;

        return $this;
    }

    public function getPieceNumero(): ?string
    {
        return $this->pieceNumero;
    }

    public function setPieceNumero(string $pieceNumero): static
    {
        $this->pieceNumero = $pieceNumero;

        return $this;
    }

    public function getDateEntree(): ?\DateTime
    {
        return $this->dateEntree;
    }

    public function setDateEntree(?\DateTimeInterface $dateEntree): static
    {
        $this->dateEntree = $dateEntree instanceof \DateTimeImmutable
        ? \DateTime::createFromImmutable($dateEntree)
        : $dateEntree;

        return $this;
    }

    public function getDateSaisie(): ?\DateTime
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(\DateTime $dateSaisie): static
    {
        $this->dateSaisie = $dateSaisie;

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

    public function getPersonnePrevenirNom(): ?string
    {
        return $this->personnePrevenirNom;
    }

    public function setPersonnePrevenirNom(?string $personnePrevenirNom): static
    {
        $this->personnePrevenirNom = $personnePrevenirNom;

        return $this;
    }

    public function getPersonnePrevenirLien(): ?string
    {
        return $this->personnePrevenirLien;
    }

    public function setPersonnePrevenirLien(?string $personnePrevenirLien): static
    {
        $this->personnePrevenirLien = $personnePrevenirLien;

        return $this;
    }

    public function getPersonnePrevenirTel(): ?string
    {
        return $this->personnePrevenirTel;
    }

    public function setPersonnePrevenirTel(?string $personnePrevenirTel): static
    {
        $this->personnePrevenirTel = $personnePrevenirTel;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getCreePart(): ?User
    {
        return $this->creePart;
    }

    public function setCreePart(?User $creePart): static
    {
        $this->creePart = $creePart;

        return $this;
    }

    /**
     * @return Collection<int, AdhesionFonds>
     */
    public function getAdhesionsFonds(): Collection
    {
        return $this->adhesionsFonds;
    }

    public function addAdhesionsFond(AdhesionFonds $adhesionsFond): static
    {
        if (!$this->adhesionsFonds->contains($adhesionsFond)) {
            $this->adhesionsFonds->add($adhesionsFond);
            $adhesionsFond->setMembre($this);
        }

        return $this;
    }

    public function removeAdhesionsFond(AdhesionFonds $adhesionsFond): static
    {
        if ($this->adhesionsFonds->removeElement($adhesionsFond)) {
            // set the owning side to null (unless already changed)
            if ($adhesionsFond->getMembre() === $this) {
                $adhesionsFond->setMembre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlanRegularisation>
     */
    public function getPlansRegularisation(): Collection
    {
        return $this->plansRegularisation;
    }

    public function addPlansRegularisation(PlanRegularisation $plansRegularisation): static
    {
        if (!$this->plansRegularisation->contains($plansRegularisation)) {
            $this->plansRegularisation->add($plansRegularisation);
            $plansRegularisation->setMembre($this);
        }

        return $this;
    }

    public function removePlansRegularisation(PlanRegularisation $plansRegularisation): static
    {
        if ($this->plansRegularisation->removeElement($plansRegularisation)) {
            // set the owning side to null (unless already changed)
            if ($plansRegularisation->getMembre() === $this) {
                $plansRegularisation->setMembre(null);
            }
        }

        return $this;
    }
}
