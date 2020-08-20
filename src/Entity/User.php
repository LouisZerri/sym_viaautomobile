<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'utilisateur est déjà enregistré"
 * )
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/", message="La date de naissance n'est pas au bon format")
     */
    private $date_naissance;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $site_rattachement;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $enseigne;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{9,}$/", message="Le mot de passe est trop faible")
     * @Assert\Length(min="9", minMessage="Le mot de passe doit faire au minimum 9 caractères")
     * @Assert\EqualTo(propertyPath="confirm_password", message="Les mots de passe ne correspondent pas")
     * @Assert\NotBlank
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passe ne correspondent pas")
     * @Assert\NotBlank
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $portable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmation_cle;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $confirmed_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reset_token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $reset_at;

    /**
     * @ORM\OneToOne(targetEntity=Mandat::class, mappedBy="users", cascade={"persist", "remove"})
     */
    private $mandat;

    /**
     * @ORM\OneToMany(targetEntity=Vente::class, mappedBy="users")
     */
    private $ventes;

    /**
     * @ORM\OneToMany(targetEntity=MandatHistoric::class, mappedBy="users")
     */
    private $mandatHistorics;

    /**
     * @ORM\OneToMany(targetEntity=VenteHistorique::class, mappedBy="users")
     */
    private $venteHistoriques;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $roles;

    public function __construct()
    {
        $this->ventes = new ArrayCollection();
        $this->mandatHistorics = new ArrayCollection();
        $this->venteHistoriques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?string
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(string $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getSiteRattachement(): ?string
    {
        return $this->site_rattachement;
    }

    public function setSiteRattachement(string $site_rattachement): self
    {
        $this->site_rattachement = $site_rattachement;

        return $this;
    }

    public function getEnseigne(): ?string
    {
        return $this->enseigne;
    }

    public function setEnseigne(string $enseigne): self
    {
        $this->enseigne = $enseigne;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPortable(): ?string
    {
        return $this->portable;
    }

    public function setPortable(string $portable): self
    {
        $this->portable = $portable;

        return $this;
    }

    public function getConfirmationCle(): ?string
    {
        return $this->confirmation_cle;
    }

    public function setConfirmationCle(?string $confirmation_cle): self
    {
        $this->confirmation_cle = $confirmation_cle;

        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmed_at;
    }

    public function setConfirmedAt(?\DateTimeInterface $confirmed_at): self
    {
        $this->confirmed_at = $confirmed_at;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    public function getResetAt(): ?\DateTimeInterface
    {
        return $this->reset_at;
    }

    public function setResetAt(?\DateTimeInterface $reset_at): self
    {
        $this->reset_at = $reset_at;

        return $this;
    }

    public function str_random($length)
    {
        $alphabet = "abcdefghijklmnopqrstuvwxyz012345689";
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return array($this->roles);
    }

    public function setRoles(string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function serialize()
    {
        return serialize([$this->id, $this->email, $this->password]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
            ) = unserialize($serialized,['allowed_classes' => false]);
    }

    public function getMandat(): ?Mandat
    {
        return $this->mandat;
    }

    public function setMandat(Mandat $mandat): self
    {
        $this->mandat = $mandat;

        // set the owning side of the relation if necessary
        if ($mandat->getUsers() !== $this) {
            $mandat->setUsers($this);
        }

        return $this;
    }

    /**
     * @return Collection|Vente[]
     */
    public function getVentes(): Collection
    {
        return $this->ventes;
    }

    public function addVente(Vente $vente): self
    {
        if (!$this->ventes->contains($vente)) {
            $this->ventes[] = $vente;
            $vente->setUsers($this);
        }

        return $this;
    }

    public function removeVente(Vente $vente): self
    {
        if ($this->ventes->contains($vente)) {
            $this->ventes->removeElement($vente);
            // set the owning side to null (unless already changed)
            if ($vente->getUsers() === $this) {
                $vente->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MandatHistoric[]
     */
    public function getMandatHistorics(): Collection
    {
        return $this->mandatHistorics;
    }

    public function addMandatHistoric(MandatHistoric $mandatHistoric): self
    {
        if (!$this->mandatHistorics->contains($mandatHistoric)) {
            $this->mandatHistorics[] = $mandatHistoric;
            $mandatHistoric->setUsers($this);
        }

        return $this;
    }

    public function removeMandatHistoric(MandatHistoric $mandatHistoric): self
    {
        if ($this->mandatHistorics->contains($mandatHistoric)) {
            $this->mandatHistorics->removeElement($mandatHistoric);
            // set the owning side to null (unless already changed)
            if ($mandatHistoric->getUsers() === $this) {
                $mandatHistoric->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VenteHistorique[]
     */
    public function getVenteHistoriques(): Collection
    {
        return $this->venteHistoriques;
    }

    public function addVenteHistorique(VenteHistorique $venteHistorique): self
    {
        if (!$this->venteHistoriques->contains($venteHistorique)) {
            $this->venteHistoriques[] = $venteHistorique;
            $venteHistorique->setUsers($this);
        }

        return $this;
    }

    public function removeVenteHistorique(VenteHistorique $venteHistorique): self
    {
        if ($this->venteHistoriques->contains($venteHistorique)) {
            $this->venteHistoriques->removeElement($venteHistorique);
            // set the owning side to null (unless already changed)
            if ($venteHistorique->getUsers() === $this) {
                $venteHistorique->setUsers(null);
            }
        }

        return $this;
    }

}
