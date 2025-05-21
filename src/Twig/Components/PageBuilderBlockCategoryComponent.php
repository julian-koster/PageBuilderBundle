<?php

namespace JulianKoster\PageBuilderBundle\Twig\Components;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockCategory;
use App\Form\Other\PageBuilderBlockCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('PageBuilderBlockCategoryComponent')]
final class PageBuilderBlockCategoryComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true)]
    public ?bool $showSuccessMessage = false;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PageBuilderBlockCategoryType::class);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager): void
    {
        $this->submitForm();
        $form = $this->form;

        /** @var PageBuilderBlockCategory $category */
        $category = $form->getData();
        $entityManager->persist($category);
        $entityManager->flush();

        $this->emitUp('rerender:component');

        $this->showSuccessMessage = true;
    }
}
