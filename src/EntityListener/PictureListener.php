<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Picture;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * class PictureListener
 * @package App\EntityListener
 */
class PictureListener
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var string
     */
    private string $picturesDir;

    /**
     * @var string
     */
    private string $picturesAbsoluteDir;

    /**
     * PictureListener constructor
     * @param string $picturesDir
     * @param string $picturesAbsoluteDir
     */
    public function __construct(
        string $picturesDir,
        string $picturesAbsoluteDir,
        SluggerInterface $slugger
    ) {
        $this->picturesDir = $picturesDir;
        $this->picturesAbsoluteDir = $picturesAbsoluteDir;
        $this->slugger = $slugger;
    }

    /**
     * @param Picture $picture
     */
    public function prePersist(Picture $picture): void
    {
        $this->upload($picture);
    }

    /**
     * @param Picture $picture
     */
    public function preUpdate(Picture $picture): void
    {
        $this->upload($picture);
    }

    /**
     * Upload image if instance of UploadedFile
     *
     * @param Picture $picture
     * @return void
     */
    private function upload(Picture $picture): void
    {
        $file = $picture->getFile();

        if ($file instanceof UploadedFile) {

            $originalFilename = pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            );
            $safeFilename = strtolower(
                $this->slugger->slug($originalFilename)->toString()
            );
            $filename = $safeFilename . '-' . uniqid() . '.' . $file->guessClientExtension();

            $file->move($this->picturesAbsoluteDir, $filename);

            $picture->setPath(
                sprintf(
                    '%s/%s',
                    $this->picturesDir,
                    $filename
                )
            );
        }
    }
}
