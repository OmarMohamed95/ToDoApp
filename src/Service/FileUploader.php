<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class FileUploader
{
    private $targetDirectory;
    private $directoryName;
    private $fileName;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function setDirectoryName(string $directoryName)
    {
        $this->directoryName = $directoryName;
    }

    public function getDirectoryName()
    {
        return $this->directoryName;
    }

    private function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
    
    private function naming(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $this->setFileName($fileName);
    }

    public function upload(UploadedFile $file)
    {

        $this->naming($file);
        $fileName = $this->getFileName();

        try {
            $file->move($this->getTargetDirectory() . $this->getDirectoryName(), $fileName);
        } catch (FileException $e) {
            
        }
    }

    private function setFileName($fileName)
    {
        return $this->fileName = $fileName;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

}