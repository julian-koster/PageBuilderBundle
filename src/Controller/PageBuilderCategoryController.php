<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockCategory;
use JulianKoster\PageBuilderBundle\Form\PageBuilderBlockCategoryType;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockCategoryRepository;
use JulianKoster\PageBuilderBundle\Service\AdminRolesChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/category')]
class PageBuilderCategoryController extends AbstractController
{
    #[Route('/index', name: 'juliankoster_pagebuilder_mainbuilder_render_block_category_index', methods: ['GET'])]
    public function render_category_index(PageBuilderBlockCategoryRepository $repository, AdminRolesChecker $adminRolesChecker): Response
    {
        if(!$adminRolesChecker->checkRoles())
        {
            throw new AccessDeniedHttpException();
        }

        return $this->render('@PageBuilderBundle/ui/category/index.html.twig', [
            'categories' => $repository->findBy([], ["name" => "ASC"]),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/crud/{operation}/{category}',
        name: 'juliankoster_pagebuilder_mainbuilder_render_block_category_crud',
    )]
    public function render_crud(
        string                   $operation,
        EntityManagerInterface   $entityManager,
        TranslatorInterface      $translator,
        Request                  $request,
        AdminRolesChecker        $adminRolesChecker,
        PageBuilderBlockCategory $category = null
    ): Response
    {
        if(!$adminRolesChecker->checkRoles())
        {
            throw new AccessDeniedHttpException();
        }

        if ($operation == "delete") {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('Category deleted', domain: 'admin_page_builder'));
            return $this->redirectToRoute('app_admin_page_builder_block_category_index');
        }

        $form = $this->createForm(PageBuilderBlockCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($operation == "other") {
                $block = $form->getData();

                $entityManager->persist($block);

                $entityManager->flush();
                $this->addFlash('success', $translator->trans('Category saved'));
                return $this->redirectToRoute('app_admin_page_builder_block_category_index');
            }
        }

        return $this->render('@PageBuilderBundle/ui/category/crud.html.twig', [
            'form' => $form,
            'category' => $category,
        ]);
    }
}