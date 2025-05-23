<?php

namespace JulianKoster\PageBuilderBundle;

use JulianKoster\PageBuilderBundle\DependencyInjection\ConfigValidator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\AssetMapper\AssetMapperInterface;

class PageBuilderBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                # The template_dir holds the user defined Twig templates
                ->scalarNode('template_dir')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                # The image dir contains uploads from image fields inside the PageBuilder's preview mode.
                ->scalarNode('image_dir')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        ConfigValidator::validate($config, $builder);

        $container->parameters()
            ->set('page_builder.template_dir', $config['template_dir'])
            ->set('page_builder.image_dir', $config['image_dir']);

        $container->import(__DIR__ . '/../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        # Path to the PageBuilder's UI
        $builder->prependExtensionConfig('twig', [
            'paths' => [
                __DIR__.'/../templates' => 'PageBuilderBundle',
            ],
        ]);

        $builder->prependExtensionConfig('twig_component', [
            'defaults' => [
                'JulianKoster\PageBuilderBundle\Twig\Components\\' => '@PageBuilderBundle/components',
            ],
        ]);

        $config = $builder->getExtensionConfig('page_builder')[0] ?? [];

        $resolvedPath = $builder->getParameterBag()->resolveValue($config['template_dir']);

        # Path to the user defined (created) Twig blocks
        $builder->prependExtensionConfig('twig', [
            'paths' => [
                $resolvedPath => 'PageBuilderUserBlocks',
            ],
        ]);

        # Currently, this bundle is only tested to work with AssetMapper.
        # TODO: Add support for Webpack (and other ways of including the assets).
        if (!interface_exists(AssetMapperInterface::class)) {
            return;
        }

        # Register with AssetMapper for the bundle's Stimulus controllers (and other assets).
        $builder->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__ . '/../assets/dist' => '@juliankoster/pagebuilderbundle',
                ],
            ],
        ]);
    }
}
