<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    #[Route('/live/{id}', name: 'app_admin_page_builder_preview')]
    public function preview(
        PageBuilderPage $page,
        Environment $twig,
        PageBuilderBlockRepository $blockRepo
    ): Response {
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