<?php

namespace JulianKoster\PageBuilderBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use JulianKoster\PageBuilderBundle\DependencyInjection\PageBuilderExtension;

class PageBuilderBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new PageBuilderExtension();
    }
}