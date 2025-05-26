<?php

namespace JulianKoster\PageBuilderBundle\Twig\Components;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('LinkTypeRouteAutocompleteComponent')]
class LinkTypeRouteAutocompleteComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp]
    public ?string $value = null;

    #[LiveProp]
    public ?string $instanceId = null;

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    /**
     * @return array
     */
    public function getUserDefinedRoutes(): array
    {
        return $this->parameterBag->get('page_builder.user_facing_routes') ?? [];
    }
}