<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trick;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Un chemin vers la photo doit être saisi.")
     * @Assert\Length(min=3, max=255, minMessage="Le chemin doit faire au moins 3 caractères.")
     */
    private string $url;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Trick $trick = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
