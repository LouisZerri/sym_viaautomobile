<?php

namespace App\Entity;

use App\Repository\VenteHistoriqueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VenteHistoriqueRepository::class)
 */
class VenteHistorique
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_vente;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $immatriculation;

    /**
     * @ORM\Column(type="integer")
     */
    private $livree;

    /**
     * @ORM\Column(type="integer")
     */
    private $frais_mer;

    /**
     * @ORM\Column(type="integer")
     */
    private $garantie;

    /**
     * @ORM\Column(type="integer")
     */
    private $financement;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="venteHistoriques")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateVente(): ?\DateTimeInterface
    {
        return $this->date_vente;
    }

    public function setDateVente(\DateTimeInterface $date_vente): self
    {
        $this->date_vente = $date_vente;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getLivree(): ?int
    {
        return $this->livree;
    }

    public function setLivree(int $livree): self
    {
        $this->livree = $livree;

        return $this;
    }

    public function getFraisMer(): ?int
    {
        return $this->frais_mer;
    }

    public function setFraisMer(int $frais_mer): self
    {
        $this->frais_mer = $frais_mer;

        return $this;
    }

    public function getGarantie(): ?int
    {
        return $this->garantie;
    }

    public function setGarantie(int $garantie): self
    {
        $this->garantie = $garantie;

        return $this;
    }

    public function getFinancement(): ?int
    {
        return $this->financement;
    }

    public function setFinancement(int $financement): self
    {
        $this->financement = $financement;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }
}
