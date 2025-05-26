<?php

namespace JulianKoster\PageBuilderBundle\Helper;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * Processes uploads, created to make the image upload process in blocks a bit easier.
 */
readonly class UploadHelper
{
    public function __construct(
        private SluggerInterface      $slugger,
        private ContainerBagInterface $containerBag,
    )
    {
    }

    private function resolveUploadDir(string $uploadDir): string|null
    {
        if ($this->containerBag->has($uploadDir))
        {
            return $this->containerBag->get($uploadDir);
        }
        else
        {
            return null;
        }
    }

    public function renameUploadedFile(UploadedFile $uploadedFile): string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        return $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
    }

    public function moveUploadedFile(UploadedFile $uploadedFile, string $fileName, $destination): void
    {
        $destinationPath = $this->resolveUploadDir($destination);

        try {
            $uploadedFile->move(
                $destinationPath,
                $fileName
            );
            $this->setFilePermissions($fileName, $destination);
        } catch (FileException $e) {
            throw new \ErrorException('File move error');
        }
    }

    public function setFilePermissions(string $fileName, string $destination): void
    {
        $uploadDir = $this->resolveUploadDir($destination);

        $filesystem = new Filesystem();
        $filesystem->chmod($uploadDir . '/' . $fileName, 0644);
    }

    /**
     * @throws \ErrorException
     */
    public function processUpload(UploadedFile $uploadedFile, ?string $destination = null): string
    {
        $fileName = $this->renameUploadedFile($uploadedFile);
        $this->moveUploadedFile($uploadedFile, $fileName, $destination);

        return $fileName;
    }
}