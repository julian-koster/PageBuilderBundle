<?php

namespace JulianKoster\PageBuilderBundle\Twig\Extension;

use JulianKoster\PageBuilderBundle\Twig\Runtime\PageBuilderExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PageBuilderExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            // new TwigFilter('filter_name', [PageBuilderExtensionRuntime::class, 'doSomething']),
            new TwigFilter('sentenceCase', [PageBuilderExtensionRuntime::class, 'sentenceCase']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'pb_block_config',
                [PageBuilderExtensionRuntime::class, 'configureBlockSetting'],
                ['needs_context' => true, 'is_safe' => ['html']]
            ),
            new TwigFunction('pb_block_render_inputs', [PageBuilderExtensionRuntime::class, 'renderBlockInputs']),
        ];
    }
}
