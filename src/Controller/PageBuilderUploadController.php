<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/upload')]
final class PageBuilderUploadController extends AbstractController
{
    #[Route('/image', name: 'app_admin_page_builder_upload_image')]
    public function upload_image_api(Request $request, SluggerInterface $slugger): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $files = $request->files->get('files'); // array of UploadedFile

        if(!$files)
        {
            return new JsonResponse(["text" => "No file(s) received.", 400]);
        }

        foreach($files as $file)
        {
            if(!$file->isValid())
            {
                return new JsonResponse(["text" => "Invalid file(s) received.", 400]);
            }

            if(!$file instanceof UploadedFile)
            {
                return new JsonResponse(["text" => "Invalid file(s) received.", 400]);
            }

            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFileName = $slugger->slug($originalFilename);
            $newFilename = $safeFileName.'-'.uniqid().'.'.$file->guessExtension();

            try {
                $file->move($this->getParameter('public_upload_dir'), $newFilename);
                return new JsonResponse(["text" => "File uploaded successfully.", "uploadedFilename" => $newFilename], 200);
            } catch (FileException $e) {
                return new JsonResponse(["text" => "File upload failed.", 400]);
            }
        }

        return new JsonResponse("File uploaded.", 200);
    }
}
