<?php

namespace JulianKoster\PageBuilderBundle\Twig\Components;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use App\Form\PageBuilder\PageBuilderBlockType;
use App\Service\Helpers\UploadHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('PageBuilderBlockComponent')]
final class PageBuilderBlockComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public ?PageBuilderBlock $pageBuilderBlock = null;

    #[LiveProp(writable: true)]
    public ?string $screenshot = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PageBuilderBlockType::class, $this->pageBuilderBlock);
    }

    #[LiveListener('rerender:component')]
    public function rerender(): void
    {
    }

    #[LiveAction]
    public function upload(Request $request, UploadHelper $uploadHelper): void
    {
        $file = $request->files->get('block_screenshot');
        $fileName = $uploadHelper->processUpload($file, 'public_upload_dir');

        $this->screenshot = $fileName;
    }

    #[LiveAction]
    public function save(
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
    ): void
    {
        $this->submitForm();
        $form = $this->getForm();

        /** @var PageBuilderBlock $block */
        $block = $form->getData();

        // Only update the screenshot if a new one was uploaded
        if ($this->screenshot && $this->screenshot !== $block->getScreenshot()) {
            $block->setScreenshot($this->screenshot);
        }

        $entityManager->persist($block);
        $entityManager->flush();

        $this->dispatchBrowserEvent('notification:create', [
            'title' => $translator->trans('Success', domain: 'admin_page_builder'),
            'text' => $translator->trans('Block saved.', domain: 'admin_page_builder'),
        ]);
    }
}
