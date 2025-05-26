<?php

declare(strict_types=1);

namespace JulianKoster\PageBuilderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class PrivateFileRenderController extends AbstractController
{
    #[Route('/private-file/{filename}', name: 'juliankoster_pagebuilder_mainbuilder_serve_private_file')]
    public function servePrivateFile(string $filename): BinaryFileResponse
    {
        $privateDir = $this->getParameter('kernel.project_dir') . '/private_uploads';

        $filePath = $privateDir . '/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE, // or 'attachment' to force download
            $filename
        );

        return $response;
    }
}
