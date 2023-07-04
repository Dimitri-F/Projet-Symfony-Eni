<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FileUploaderService
{
    private $targetDirectory;

    private $session;

    public function __construct($targetDirectory, SessionInterface $session)
    {
        $this->targetDirectory = $targetDirectory;
        $this->session = $session;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            $errorMessage = 'Une erreur s\'est produite lors de l\'upload du fichier : ' . $e->getMessage();
            $this->session->getFlashBag()->add('error', $errorMessage);
            return null;
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}