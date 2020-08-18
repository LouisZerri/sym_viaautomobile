<?php

namespace App\Entity;

use App\Repository\MandatHistoricRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MandatHistoricRepository::class)
 */
class MandatHistoric
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_mandat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="mandatHistorics")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDateMandat(): ?\DateTimeInterface
    {
        return $this->date_mandat;
    }

    public function setDateMandat(\DateTimeInterface $date_mandat): self
    {
        $this->date_mandat = $date_mandat;

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
