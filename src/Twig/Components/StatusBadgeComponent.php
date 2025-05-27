<?php

namespace JulianKoster\PageBuilderBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('StatusBadgeComponent')]
class StatusBadgeComponent
{
    public ?string $status = null;
}