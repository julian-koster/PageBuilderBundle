<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use JulianKoster\PageBuilderBundle\Form\PageBuilderBlockType;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use JulianKoster\PageBuilderBundle\Service\AdminRolesChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/block')]
final class PageBuilderBlockController extends AbstractController
{
    #[Route('/index', name: 'juliankoster_pagebuilder_mainbuilder_render_block_index', methods: ['GET'])]
    public function render_block_index(PageBuilderBlockRepository $repository, AdminRolesChecker $adminRolesChecker): Response
    {
        if(!$adminRolesChecker->checkRoles())
        {
            throw new AccessDeniedHttpException();
        }

        return $this->render('@PageBuilderBundle/ui/block/index.html.twig', [
            'blocks' => $repository->findBy([], ["name" => "ASC"]),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/crud/{operation}/{block}',
        name: 'juliankoster_pagebuilder_mainbuilder_render_block_crud',
        requirements: [
            '_locale' => '%app.supported_locales%',
        ],
    )]
    public function render_block_crud(
        string                 $operation,
        EntityManagerInterface $entityManager,
        TranslatorInterface    $translator,
        Request                $request,
        AdminRolesChecker      $adminRolesChecker,
        PageBuilderBlock       $block = null,
    ): Response
    {
        if(!$adminRolesChecker->checkRoles())
        {
            throw new AccessDeniedHttpException();
        }

        if ($operation == "delete") {
            $entityManager->remove($block);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('Block deleted', domain: 'admin_page_builder'));
            return $this->redirectToRoute('app_admin_page_builder_block_index');
        }

        $form = $this->createForm(PageBuilderBlockType::class, $block);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($operation == "other") {
                $block = $form->getData();

                $entityManager->persist($block);

                $entityManager->flush();
                $this->addFlash('success', $translator->trans('Block saved'));
                return $this->redirectToRoute('app_admin_page_builder_block_index');
            }
        }

        return $this->render('@PageBuilderBundle/ui/block/crud.html.twig', [
            'form' => $form,
            'block' => $block,
        ]);
    }
}
