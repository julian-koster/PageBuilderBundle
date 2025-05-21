<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use JulianKoster\PageBuilderBundle\Form\PageBuilderPageType;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/page')]
final class PageBuilderPageController extends AbstractController
{
    #[Route('/index', name: 'app_admin_page_builder_page_index', methods: ['GET'])]
    public function render_index(PageBuilderPageRepository $repository): Response
    {
        return $this->render('@PageBuilderBundle/page_builder/ui/page/index.html.twig', [
            'pages' => $repository->findAll(),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/crud/{operation}/{page}',
        name: 'app_admin_page_builder_page_crud',
        requirements: [
            '_locale' => '%app.supported_locales%',
        ],
    )]
    public function render_crud(
        string $operation,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Request $request,
        PageBuilderPage $page = null
    ): Response
    {
        if ($operation == "delete") {
            $entityManager->remove($page);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('Page deleted', domain: 'admin_page_builder'));
            return $this->redirectToRoute('app_admin_page_builder_page_index');
        }

        $form = $this->createForm(PageBuilderPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($operation == "other") {
                $page = $form->getData();

                $entityManager->persist($page);

                $entityManager->flush();
                $this->addFlash('success', $translator->trans('Page saved'));
                return $this->redirectToRoute('app_admin_page_builder_page_index');
            }
        }

        return $this->render('@PageBuilderBundle/page_builder/ui/page/crud.html.twig', [
            'form' => $form,
            'page' => $page,
        ]);
    }
}
