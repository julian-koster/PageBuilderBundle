<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockRepository;
use JulianKoster\PageBuilderBundle\Service\AdminRolesChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/preview')]
class PageBuilderPreviewController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/live/{id}', name: 'juliankoster_pagebuilder_mainbuilder_render_live_preview')]
    public function preview(
        PageBuilderPage            $page,
        Environment                $twig,
        PageBuilderBlockRepository $blockRepo,
        AdminRolesChecker          $adminRolesChecker,
    ): Response {

        if($this->getParameter('page_builder.page_builder.allow_anonymous_previews')) {
            $this->denyAccessUnlessGranted('ANONYMOUS');
        }
        else {
            if(!$adminRolesChecker->checkRoles())
            {
                throw new AccessDeniedHttpException();
            }
        }

        $blocks = [];

        foreach ($page->getBlockInstance() ?? [] as $entry) {
            $instanceId = $entry->getInstanceId() ?? null;
            $blockId = $entry->getPageBuilderBlock()->getId() ?? null;

            if (!$instanceId) {
                continue;
            }

            $block = $blockRepo->find($blockId);

            if (!$block) {
                continue;
            }

            $html = $twig->render('/' . $block->getTwigTemplatePath(), [
                'instanceId' => $instanceId,
            ]);

            $blocks[] = $html;
        }

        return $this->render('@PageBuilderBundle/ui/preview/preview.html.twig', [
            'page' => $page,
            'blocks' => $blocks,
        ]);
    }
}