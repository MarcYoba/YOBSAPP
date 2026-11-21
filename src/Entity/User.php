<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $nomComplet = 'Inconue';

    /**
     * @var Collection<int, AdhesionFonds>
     */
    #[ORM\OneToMany(targetEntity: AdhesionFonds::class, mappedBy: 'user')]
    private Collection $adhesionFonds;

    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, PlanRegularisation>
     */
    #[ORM\OneToMany(targetEntity: PlanRegularisation::class, mappedBy: 'user')]
    private Collection $planRegularisations;

    /**
     * @var Collection<int, Membre>
     */
    #[ORM\OneToMany(targetEntity: Membre::class, mappedBy: 'utilisateur')]
    private Collection $membres;

    /**
     * @var Collection<int, Fonds>
     */
    #[ORM\OneToMany(targetEntity: Fonds::class, mappedBy: 'user')]
    private Collection $fonds;

    public function __construct()
    {
        $this->adhesionFonds = new ArrayCollection();
        $this->planRegularisations = new ArrayCollection();
        $this->membres = new ArrayCollection();
        $this->fonds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): static
    {
        $this->nomComplet = $nomComplet;

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
            $adhesionFond->setUser($this);
        }

        return $this;
    }

    public function removeAdhesionFond(AdhesionFonds $adhesionFond): static
    {
        if ($this->adhesionFonds->removeElement($adhesionFond)) {
            // set the owning side to null (unless already changed)
            if ($adhesionFond->getUser() === $this) {
                $adhesionFond->setUser(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
            $planRegularisation->setUser($this);
        }

        return $this;
    }

    public function removePlanRegularisation(PlanRegularisation $planRegularisation): static
    {
        if ($this->planRegularisations->removeElement($planRegularisation)) {
            // set the owning side to null (unless already changed)
            if ($planRegularisation->getUser() === $this) {
                $planRegularisation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Membre>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Membre $membre): static
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->setUtilisateur($this);
        }

        return $this;
    }

    public function removeMembre(Membre $membre): static
    {
        if ($this->membres->removeElement($membre)) {
            // set the owning side to null (unless already changed)
            if ($membre->getUtilisateur() === $this) {
                $membre->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fonds>
     */
    public function getFonds(): Collection
    {
        return $this->fonds;
    }

    public function addFond(Fonds $fond): static
    {
        if (!$this->fonds->contains($fond)) {
            $this->fonds->add($fond);
            $fond->setUser($this);
        }

        return $this;
    }

    public function removeFond(Fonds $fond): static
    {
        if ($this->fonds->removeElement($fond)) {
            // set the owning side to null (unless already changed)
            if ($fond->getUser() === $this) {
                $fond->setUser(null);
            }
        }

        return $this;
    }
}
