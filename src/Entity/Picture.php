<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trick;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Picture
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 * @ORM\EntityListeners({"App\EntityListener\PictureListener"})
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
     */
    private ?string $path = null;

    /**
     * @var UploadedFile|null
     */
    private UploadedFile $file;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Trick $trick = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

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

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile|null $file
     */
    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }
}
