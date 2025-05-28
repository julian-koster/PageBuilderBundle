<?php

namespace JulianKoster\PageBuilderBundle\Controller;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use JulianKoster\PageBuilderBundle\Enum\PageBuilderPageStatus;
use JulianKoster\PageBuilderBundle\Form\PageBuilderPageType;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use JulianKoster\PageBuilderBundle\Service\AdminRolesChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/page')]
final class PageBuilderPageController extends AbstractController
{
    #[Route('/index', name: 'juliankoster_pagebuilder_mainbuilder_render_page_index', methods: ['GET'])]
    public function render_index(PageBuilderPageRepository $repository, AdminRolesChecker $adminRolesChecker): Response
    {
        if(!$adminRolesChecker->checkRoles())
        {
            throw new AccessDeniedHttpException();
        }

        return $this->render('@PageBuilderBundle/ui/page/index.html.twig', [
            'pages' => $repository->findAll(),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/crud/{operation}/{page}',
        name: 'juliankoster_pagebuilder_mainbuilder_render_page_crud',
        requirements: [
            '_locale' => '%app.supported_locales%',
        ],
    )]
    public function render_crud(
        string                 $operation,
        EntityManagerInterface $entityManager,
        TranslatorInterface    $translator,
        Request                $request,
        AdminRolesChecker      $adminRolesChecker,
        PageBuilderPage        $page = null
    ): Response
    {
        if(!$adminRolesChecker->checkRoles())
        {
            throw new AccessDeniedHttpException();
        }

        if ($operation == "delete") {
            $entityManager->remove($page);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('Page deleted', domain: 'admin_page_builder'));
            return $this->redirectToRoute('juliankoster_pagebuilder_mainbuilder_render_page_index');
        }

        $form = $this->createForm(PageBuilderPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($operation == "other") {

                /** @var PageBuilderPage $page */
                $page = $form->getData();
                $page->setStatus(PageBuilderPageStatus::DRAFT->value);

                $entityManager->persist($page);

                $entityManager->flush();
                $this->addFlash('success', $translator->trans('Page saved'));
                return $this->redirectToRoute('juliankoster_pagebuilder_mainbuilder_render_page_index');
            }
        }

        return $this->render('@PageBuilderBundle/ui/page/crud.html.twig', [
            'form' => $form,
            'page' => $page,
        ]);
    }
}
