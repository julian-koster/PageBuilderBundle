<?php

namespace JulianKoster\PageBuilderBundle\Tests\TestApplication;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use JulianKoster\PageBuilderBundle\PageBuilderBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\UX\LiveComponent\LiveComponentBundle;
use Symfony\UX\StimulusBundle\StimulusBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;

final class Kernel extends BaseKernel implements KernelInterface
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle(),
            new PageBuilderBundle(),
            new LiveComponentBundle(),
            new TwigComponentBundle(),
            new StimulusBundle(),
            new DoctrineBundle()
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'S3CRET',
            'test' => true,
            'translator' => [
                'default_path' => '%kernel.project_dir%/translations',
                'fallbacks' => ['en'],
            ],
        ]);

        $container->extension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'path' => '%kernel.cache_dir%/test_database.sqlite',
            ],

            'orm' => [
                'auto_generate_proxy_classes' => true,
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping' => true,
                'mappings' => [
                    'TestEntities' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/src/Entity',
                        'prefix' => 'EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity',
                        'alias' => 'app',
                    ],
                ],
            ],
        ]);

        $container->extension('twig', [
            'default_path' => '%kernel.project_dir%/tests/Fixtures/templates',
        ]);

        $container->extension('page_builder', [
            'template_dir' => __DIR__ . '/templates/user_blocks',
            'image_dir' => '%kernel.project_dir%/public/uploads/pagebuilder',
            'user_facing_routes' => [
                ['route' => 'homepage', 'name' => 'Home', 'description' => 'Homepage'],
            ],
            'admin_roles' => ['ROLE_ADMIN'],
            'allow_anonymous_previews' => false,
            'use_parent_child_structure' => true,
        ]);

        $container->extension('security', [
            'providers' => [
                'in_memory' => [
                    'memory' => [
                        'users' => [
                            'admin' => ['password' => 'pass', 'roles' => ['ROLE_ADMIN']],
                        ],
                    ],
                ],
            ],
            'firewalls' => [
                'main' => [
                    'lazy' => true,
                    'provider' => 'in_memory',
                ],
            ],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // No-op or minimal routes if you test controllers
    }
}
