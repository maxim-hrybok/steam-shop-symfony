<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        // Attribute to inject the target directory for file uploads from the service configuration
        #[Autowire('%kernel.project_dir%/public/uploads/products')]
        private string $targetDirectory,
        
        // Symfony's SluggerInterface to create safe filenames
        private SluggerInterface $slugger
    ) {}

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Make the filename safe (e.g., "Моя Картинка" becomes "Moya-Kartinka")
        $safeFilename = $this->slugger->slug($originalFilename);
        // Add a unique ID to prevent files from overwriting each other
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            // moove file to public/uploads/products
            $file->move($this->targetDirectory, $newFilename);
        } catch (FileException $e) {
            // errors can be logged here
            throw new \Exception('Error occurred while uploading the file');
        }

        return $newFilename;
    }
}