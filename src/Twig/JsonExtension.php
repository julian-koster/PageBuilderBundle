<?php

namespace JulianKoster\PageBuilderBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('json_decode', fn(string $json) => json_decode($json, true)),
        ];
    }
}