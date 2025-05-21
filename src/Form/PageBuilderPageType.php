<?php

namespace JulianKoster\PageBuilderBundle\Form;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageBuilderPageType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->translator->trans('Name for internal use'),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('title', TextType::class, [
                'label' => $this->translator->trans('Page title'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 70]),
                ],
            ])
            ->add('locale', ChoiceType::class, [
                'label' => $this->translator->trans('Page language'),
                'choices' => [
                    'Dutch' => 'nl',
                    'English' => 'en',
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans('Choose page language'),
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageBuilderPage::class,
        ]);
    }
}
