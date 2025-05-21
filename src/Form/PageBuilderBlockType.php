<?php

namespace JulianKoster\PageBuilderBundle\Form;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageBuilderBlockType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->translator->trans('Block name', domain: 'admin_page_builder'),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => PageBuilderBlockCategory::class,
                'choice_label' => 'name',
                'autocomplete' => true,
                'multiple' => true,
            ])
            ->add('twigTemplatePath', TextType::class, [
                'label' => $this->translator->trans('Template path', domain: 'admin_page_builder'),
                'help' => $this->translator->trans('Relative path from page_builder main e.g. blocks/hero.html.twig', domain: 'admin_page_builder'),
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'save',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageBuilderBlock::class,
        ]);
    }
}
