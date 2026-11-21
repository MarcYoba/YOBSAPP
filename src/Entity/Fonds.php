<?php

namespace App\Entity;

use App\Repository\FondsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FondsRepository::class)]
class Fonds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $nature = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 100)]
    private ?string $ordreAffichage = null;

    #[ORM\Column]
    private ?float $miseInitiale = null;

    #[ORM\Column(length: 50)]
    private ?string $cleDroits = null;

    #[ORM\Column(length: 100)]
    private ?string $modeValorisation = null;

    #[ORM\Column]
    private ?bool $seVend = null;

    #[ORM\Column(length: 20)]
    private ?string $dureeMax = null;

    #[ORM\Column]
    private ?float $miseAprix = null;

    #[ORM\Column]
    private ?float $tauxPlafondStatutaire = null;

    #[ORM\Column]
    private ?float $plafondAchat = null;

    #[ORM\Column]
    private ?bool $uniciteDette = null;

    #[ORM\Column(length: 100)]
    private ?string $groupeAlternance = null;

    #[ORM\Column(length: 100)]
    private ?string $destInterets = null;

    #[ORM\Column]
    private ?bool $fondsAutovente = null;

    #[ORM\Column]
    private ?float $forfaitDefaut = null;

    #[ORM\Column]
    private ?int $randCascade = null;

    #[ORM\Column]
    private ?float $chargesAvantVente = null;

    #[ORM\Column(length: 50)]
    private ?string $transfertCloture = null;

    #[ORM\Column]
    private ?float $seuilTresorerie = null;

    #[ORM\Column]
    private ?float $seuilPlancher = null;

    #[ORM\Column(length: 100)]
    private ?string $objetsFinances = null;

    #[ORM\Column(length: 20)]
    private ?string $rssExigee = null;

    #[ORM\Column(length: 50)]
    private ?string $fondsModele = null;

    #[ORM\Column(length: 255)]
    private ?string $resolutionCreation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateOuverture = null;

    #[ORM\Column]
    private ?float $soldeDisponible = null;

    #[ORM\Column]
    private ?float $encours = null;

    #[ORM\ManyToOne(inversedBy: 'fonds')]
    private ?User $user = null;

    /**
     * @var Collection<int, AdhesionFonds>
     */
    #[ORM\OneToMany(targetEntity: AdhesionFonds::class, mappedBy: 'fonds')]
    private Collection $adhesionFonds;

    /**
     * @var Collection<int, PlanRegularisation>
     */
    #[ORM\OneToMany(targetEntity: PlanRegularisation::class, mappedBy: 'fonds')]
    private Collection $planRegularisations;

    public function __construct()
    {
        $this->adhesionFonds = new ArrayCollection();
        $this->planRegularisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): static
    {
        $this->nature = $nature;

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

    public function getOrdreAffichage(): ?string
    {
        return $this->ordreAffichage;
    }

    public function setOrdreAffichage(string $ordreAffichage): static
    {
        $this->ordreAffichage = $ordreAffichage;

        return $this;
    }

    public function getMiseInitiale(): ?float
    {
        return $this->miseInitiale;
    }

    public function setMiseInitiale(float $miseInitiale): static
    {
        $this->miseInitiale = $miseInitiale;

        return $this;
    }

    public function getCleDroits(): ?string
    {
        return $this->cleDroits;
    }

    public function setCleDroits(string $cleDroits): static
    {
        $this->cleDroits = $cleDroits;

        return $this;
    }

    public function getModeValorisation(): ?string
    {
        return $this->modeValorisation;
    }

    public function setModeValorisation(string $modeValorisation): static
    {
        $this->modeValorisation = $modeValorisation;

        return $this;
    }

    public function isSeVend(): ?bool
    {
        return $this->seVend;
    }

    public function setSeVend(bool $seVend): static
    {
        $this->seVend = $seVend;

        return $this;
    }

    public function getDureeMax(): ?string
    {
        return $this->dureeMax;
    }

    public function setDureeMax(string $dureeMax): static
    {
        $this->dureeMax = $dureeMax;

        return $this;
    }

    public function getMiseAprix(): ?float
    {
        return $this->miseAprix;
    }

    public function setMiseAprix(float $miseAprix): static
    {
        $this->miseAprix = $miseAprix;

        return $this;
    }

    public function getTauxPlafondStatutaire(): ?float
    {
        return $this->tauxPlafondStatutaire;
    }

    public function setTauxPlafondStatutaire(float $tauxPlafondStatutaire): static
    {
        $this->tauxPlafondStatutaire = $tauxPlafondStatutaire;

        return $this;
    }

    public function getPlafondAchat(): ?float
    {
        return $this->plafondAchat;
    }

    public function setPlafondAchat(float $plafondAchat): static
    {
        $this->plafondAchat = $plafondAchat;

        return $this;
    }

    public function isUniciteDette(): ?bool
    {
        return $this->uniciteDette;
    }

    public function setUniciteDette(bool $uniciteDette): static
    {
        $this->uniciteDette = $uniciteDette;

        return $this;
    }

    public function getGroupeAlternance(): ?string
    {
        return $this->groupeAlternance;
    }

    public function setGroupeAlternance(string $groupeAlternance): static
    {
        $this->groupeAlternance = $groupeAlternance;

        return $this;
    }

    public function getDestInterets(): ?string
    {
        return $this->destInterets;
    }

    public function setDestInterets(string $destInterets): static
    {
        $this->destInterets = $destInterets;

        return $this;
    }

    public function isFondsAutovente(): ?bool
    {
        return $this->fondsAutovente;
    }

    public function setFondsAutovente(bool $fondsAutovente): static
    {
        $this->fondsAutovente = $fondsAutovente;

        return $this;
    }

    public function getForfaitDefaut(): ?float
    {
        return $this->forfaitDefaut;
    }

    public function setForfaitDefaut(float $forfaitDefaut): static
    {
        $this->forfaitDefaut = $forfaitDefaut;

        return $this;
    }

    public function getRandCascade(): ?int
    {
        return $this->randCascade;
    }

    public function setRandCascade(int $randCascade): static
    {
        $this->randCascade = $randCascade;

        return $this;
    }

    public function getChargesAvantVente(): ?float
    {
        return $this->chargesAvantVente;
    }

    public function setChargesAvantVente(float $chargesAvantVente): static
    {
        $this->chargesAvantVente = $chargesAvantVente;

        return $this;
    }

    public function getTransfertCloture(): ?string
    {
        return $this->transfertCloture;
    }

    public function setTransfertCloture(string $transfertCloture): static
    {
        $this->transfertCloture = $transfertCloture;

        return $this;
    }

    public function getSeuilTresorerie(): ?float
    {
        return $this->seuilTresorerie;
    }

    public function setSeuilTresorerie(float $seuilTresorerie): static
    {
        $this->seuilTresorerie = $seuilTresorerie;

        return $this;
    }

    public function getSeuilPlancher(): ?float
    {
        return $this->seuilPlancher;
    }

    public function setSeuilPlancher(float $seuilPlancher): static
    {
        $this->seuilPlancher = $seuilPlancher;

        return $this;
    }

    public function getObjetsFinances(): ?string
    {
        return $this->objetsFinances;
    }

    public function setObjetsFinances(string $objetsFinances): static
    {
        $this->objetsFinances = $objetsFinances;

        return $this;
    }

    public function getRssExigee(): ?string
    {
        return $this->rssExigee;
    }

    public function setRssExigee(string $rssExigee): static
    {
        $this->rssExigee = $rssExigee;

        return $this;
    }

    public function getFondsModele(): ?string
    {
        return $this->fondsModele;
    }

    public function setFondsModele(string $fondsModele): static
    {
        $this->fondsModele = $fondsModele;

        return $this;
    }

    public function getResolutionCreation(): ?string
    {
        return $this->resolutionCreation;
    }

    public function setResolutionCreation(string $resolutionCreation): static
    {
        $this->resolutionCreation = $resolutionCreation;

        return $this;
    }

    public function getDateOuverture(): ?\DateTime
    {
        return $this->dateOuverture;
    }

    public function setDateOuverture(\DateTime $dateOuverture): static
    {
        $this->dateOuverture = $dateOuverture;

        return $this;
    }

    public function getSoldeDisponible(): ?float
    {
        return $this->soldeDisponible;
    }

    public function setSoldeDisponible(float $soldeDisponible): static
    {
        $this->soldeDisponible = $soldeDisponible;

        return $this;
    }

    public function getEncours(): ?float
    {
        return $this->encours;
    }

    public function setEncours(float $encours): static
    {
        $this->encours = $encours;

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

    /**
     * @return Collection<int, AdhesionFonds>
     */
    public function getAdhesionFonds(): Collection
    {
        return $this->adhesionFonds;
    }

    public function addAdhesionFond(AdhesionFonds $adhesionFond): static
    {
        if (!$this->adhesionFonds->contains($adhesionFond)) {
            $this->adhesionFonds->add($adhesionFond);
            $adhesionFond->setFonds($this);
        }

        return $this;
    }

    public function removeAdhesionFond(AdhesionFonds $adhesionFond): static
    {
        if ($this->adhesionFonds->removeElement($adhesionFond)) {
            // set the owning side to null (unless already changed)
            if ($adhesionFond->getFonds() === $this) {
                $adhesionFond->setFonds(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlanRegularisation>
     */
    public function getPlanRegularisations(): Collection
    {
        return $this->planRegularisations;
    }

    public function addPlanRegularisation(PlanRegularisation $planRegularisation): static
    {
        if (!$this->planRegularisations->contains($planRegularisation)) {
            $this->planRegularisations->add($planRegularisation);
            $planRegularisation->setFonds($this);
        }

        return $this;
    }

    public function removePlanRegularisation(PlanRegularisation $planRegularisation): static
    {
        if ($this->planRegularisations->removeElement($planRegularisation)) {
            // set the owning side to null (unless already changed)
            if ($planRegularisation->getFonds() === $this) {
                $planRegularisation->setFonds(null);
            }
        }

        return $this;
    }
}
