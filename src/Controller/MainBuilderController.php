<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use JulianKoster\PageBuilderBundle\Service\AdminRolesChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/admin/page-builder/app')]
final class MainBuilderController extends AbstractController
{
    #[Route('/main-view/{page}', name: 'juliankoster_pagebuilder_mainbuilder_render_main_view_index', methods: ['GET'])]
    public function render_main_view_index(PageBuilderPage $page, AdminRolesChecker $adminRolesChecker): Response
    {
        if(!$adminRolesChecker->checkRoles())
        {
            throw new AccessDeniedHttpException();
        }

        return $this->render('@PageBuilderBundle/ui/main/builder.html.twig', [
            'page' => $page->getId(),
        ]);
    }
}
