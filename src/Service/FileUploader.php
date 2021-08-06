<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * class FileUploader
 * @package App\Service
 */
class FileUploader
{
    /** @var string */
    private $targetDirectory;

    /** @var string */
    private $displayDirectory;

    /** @var SluggerInterface $slugger */
    private $slugger;

    public function __construct(
        $targetDirectory,
        $displayDirectory,
        SluggerInterface $slugger
    ) {
        $this->targetDirectory = $targetDirectory;
        $this->displayDirectory = $displayDirectory;
        $this->slugger = $slugger;
    }

    /**
     * @param UploadedFile $file
     * @return void
     */
    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = strtolower($this->slugger->slug($originalFilename));
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    /**
     * Target directory to move the uploaded file to
     * @return string
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * Path to directory to save in db
     * @return string
     */
    public function getDisplayDirectory()
    {
        return $this->displayDirectory;
    }
}
