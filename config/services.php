<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JulianKoster\PageBuilderBundle\Controller\PageBuilderBlockController;
use JulianKoster\PageBuilderBundle\Twig\Extension\PageBuilderExtension;
use JulianKoster\PageBuilderBundle\Twig\Runtime\PageBuilderExtensionRuntime;


return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('JulianKoster\\PageBuilderBundle\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Resources,Tests}');

    $services
        ->set(PageBuilderBlockController::class)
        ->tag('controller.service_arguments');

    $services
        ->set(PageBuilderExtension::class)
        ->tag('twig.extension');

    $services->set(PageBuilderExtensionRuntime::class)
        ->tag('twig.runtime');
};