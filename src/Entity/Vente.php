<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=VenteRepository::class)
 * @UniqueEntity(
 *     fields={"immatriculation"},
 *     message="L'immatriculation du véhicule existe déjà"
 * )
 */
class Vente
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/", message="La date de la vente n'est pas au bon format")
     */
    private $date_vente;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^[A-Z]{2}[0-9]{3}[A-Z]{2}$/", message="L'immatriculation du véhicule n'est pas au bon format")
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_time;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ventes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateVente(): ?string
    {
        return $this->date_vente;
    }

    public function setDateVente(string $date_vente): self
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

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->date_time;
    }

    public function setDateTime(?\DateTimeInterface $date_time): self
    {
        $this->date_time = $date_time;

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
