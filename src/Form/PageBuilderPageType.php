<?php

namespace JulianKoster\PageBuilderBundle\Form;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class PageBuilderPageType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator, private readonly ParameterBagInterface $parameterBag)
    {
    }

    private function useParentChildStructure(): bool
    {
        if($this->parameterBag->has('page_builder.use_parent_child_structure')) {
            if($this->parameterBag->get('page_builder.use_parent_child_structure')) {
                return true;
            }
        }

        return false;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

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
            ]);

        if($this->useParentChildStructure()) {
            $builder
                ->add('childPage', CheckboxType::class, [
                    'label' => $this->translator->trans('This page is a translation of another page'),
                    'required' => false,
                ]);

            $builder->addDependent('translations', ['childPage'], function(DependentField $field, bool $childPage) {

                if(!$childPage) {
                    return;
                }

                $field->add(EntityType::class, [
                    'label' => $this->translator->trans('Original (parent) page'),
                    'class' => PageBuilderPage::class,
                    'multiple'     => true,
                    'by_reference' => false,
                    'choice_label' => function (PageBuilderPage $page): string {
                        return $page->getTitle() . ' (' . $page->getLocale() . ')';
                    }
                ]);

            });
        }

        $builder
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
