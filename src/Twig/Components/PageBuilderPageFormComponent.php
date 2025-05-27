<?php

namespace JulianKoster\PageBuilderBundle\Twig\Components;

use Doctrine\ORM\EntityManagerInterface;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use JulianKoster\PageBuilderBundle\Form\PageBuilderPageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('PageBuilderPageFormComponent')]
class PageBuilderPageFormComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PageBuilderPageType::class);
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();
        $form = $this->getForm();

        /** @var PageBuilderPage $page */
        $page = $form->getData();

        $this->entityManager->persist($page);
        $this->entityManager->flush();
    }
}