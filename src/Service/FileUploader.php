<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * FileUploader
 */
class FileUploader
{
    /** @var string */
    private $targetDirectory;

    /** @var string */
    private $directoryName;

    /** @var string */
    private $fileName;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Set directory name
     *
     * @param string $directoryName
     *
     * @return void
     */
    public function setDirectoryName(string $directoryName): void
    {
        $this->directoryName = $directoryName;
    }

    /**
     * Get directory name
     *
     * @return string
     */
    public function getDirectoryName(): string
    {
        return $this->directoryName;
    }

    /**
     * Get target directory
     *
     * @return string
     */
    private function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
    
    /**
     * Name file
     *
     * @param UploadedFile $file
     *
     * @return void
     */
    private function naming(UploadedFile $file): void
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $this->setFileName($fileName);
    }

    /**
     * Upload action
     *
     * @param UploadedFile $file
     *
     * @throws FileException
     *
     * @return void
     */
    public function upload(UploadedFile $file): void
    {
        $this->naming($file);
        $fileName = $this->getFileName();

        try {
            $file->move($this->getTargetDirectory() . $this->getDirectoryName(), $fileName);
        } catch (FileException $e) {
            trigger_error('Exception! '.$e->getMessage());
        }
    }

    /**
     * Set file name
     *
     * @param string $fileName
     *
     * @return void
     */
    private function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
