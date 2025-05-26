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
                    ->info('All the custom block templates go here.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                # The image dir contains uploads from image fields inside the PageBuilder's preview mode.
                ->scalarNode('image_dir')
                    ->info('Contains images that the user uploads in the PageBuilder.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                # Rather than relying solely on URL's, I want users to have the options to pick routes (paths in Twig).
                # E.g. a user has <a href="">Contact us!</a> field that needs a destination,
                # If app_homepage is set in user_facing_routes, the user could select app_homepage.
                ->arrayNode('user_facing_routes')
                    ->info('Define the routes / paths that the user can use inside the PageBuilder when configuring a link.')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('route')->end()
                            ->scalarNode('name')->end()
                            ->scalarNode('description')->end()
                        ->end()
                    ->end()
                ->end()
                # Pretty straightforward, who can access the admin backend. Defined as an array ['ROLE_ADMIN','ROLE_WHATEVER'], etc.
                ->arrayNode('admin_roles')
                    ->info('Define the admin roles with access to the PageBuilder\'s backend.')
                    ->scalarPrototype()->end()
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                ->end()
                # Should non-authenticated users be able to see page previews?
                ->booleanNode('allow_anonymous_previews')
                    ->info('Allow anonymous (unauthenticated) users to view page previews. False by default, only authorized users can see page previews. If set to true, everyone can view page previews.')
                    ->defaultFalse()
                ->end()
            ->end()
        ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        ConfigValidator::validate($config, $builder);

        $container->parameters()
            ->set('page_builder.template_dir', $config['template_dir'])
            ->set('page_builder.image_dir', $config['image_dir'])
            ->set('page_builder.user_facing_routes', $config['user_facing_routes'])
            ->set('page_builder.admin_roles', $config['admin_roles'])
            ->set('page_builder.allow_anonymous_previews', $config['allow_anonymous_previews']);

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
