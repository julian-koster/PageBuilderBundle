<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/admin/page-builder/app')]
final class MainBuilderController extends AbstractController
{
    #[Route('/main-view/{page}', name: 'app_admin_main_builder_view', methods: ['GET'])]
    public function index(PageBuilderPage $page): Response
    {
        return $this->render('@PageBuilderBundle/page_builder/ui/main/builder.html.twig', [
            'page' => $page->getId(),
        ]);
    }
}
