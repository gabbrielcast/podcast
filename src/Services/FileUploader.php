<?php
namespace App\Service;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {

        
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $imagenFinalName = $this->slugger->slug($name).'-'.uniqid().'.'.$file->guessExtension();


      
        try {
            $file->move(
                $this->getTargetDirectory(),
                $imagenFinalName
            );
        } catch (FileException $e) {
           throw new Exception("Exception Tryin  to Upload file", 1);
        }

        return $imagenFinalName;
    }

    public function delete($fileName)
    {
        $filesystem = new Filesystem();
        if($filesystem->exists($this->getTargetDirectory().'/'.$fileName)){
            $filesystem->remove($this->getTargetDirectory().'/'.$fileName);
            return true;
        }
        return false;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}