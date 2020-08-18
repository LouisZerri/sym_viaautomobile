<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ChallengeRepository::class)
 * @Vich\Uploadable()
 */
class Challenge
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $periode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var File|null
     * @Assert\File(
     *     mimeTypes={"image/jpg", "image/jpeg"}
     * )
     * @Vich\UploadableField(mapping="challenge_image", fileNameProperty="image")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Regex("/^[0-1]$/", message="Vous devez utiliser seulement 0 ou 1")
     */
    private $en_cours;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vainqueur;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_accueil;

    /**
     * @var File|null
     * @Assert\File(
     *     mimeTypes={"image/jpg", "image/jpeg"}
     * )
     * @Vich\UploadableField(mapping="accueil_image", fileNameProperty="image_accueil")
     */
    private $imageFileAccueil;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(string $periode): self
    {
        $this->periode = $periode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getEnCours(): ?int
    {
        return $this->en_cours;
    }

    public function setEnCours(int $en_cours): self
    {
        $this->en_cours = $en_cours;

        return $this;
    }

    public function getVainqueur(): ?string
    {
        return $this->vainqueur;
    }

    public function setVainqueur(?string $vainqueur): self
    {
        $this->vainqueur = $vainqueur;

        return $this;
    }

    public function getImageAccueil(): ?string
    {
        return $this->image_accueil;
    }

    public function setImageAccueil(?string $image_accueil): self
    {
        $this->image_accueil = $image_accueil;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Challenge
     */
    public function setImageFile(?File $imageFile): Challenge
    {
        $this->imageFile = $imageFile;
        if($this->imageFile instanceof UploadedFile)
        {
            $this->updated_at = new \DateTime('now');
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFileAccueil(): ?File
    {
        return $this->imageFileAccueil;
    }

    /**
     * @param File|null $imageFileAccueil
     * @return Challenge
     */
    public function setImageFileAccueil(?File $imageFileAccueil): Challenge
    {
        $this->imageFileAccueil = $imageFileAccueil;
        if($this->imageFileAccueil instanceof UploadedFile)
        {
            $this->updated_at = new \DateTime('now');
        }
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

}
