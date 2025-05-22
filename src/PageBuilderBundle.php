<?php

namespace JulianKoster\PageBuilderBundle;

use JulianKoster\PageBuilderBundle\DependencyInjection\ConfigValidator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PageBuilderBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('template_dir')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        ConfigValidator::validate($config, $builder);

        $container->parameters()
            ->set('page_builder.template_dir', $config['template_dir']);

        $container->import(__DIR__ . '/../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
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

        $builder->prependExtensionConfig('twig', [
            'paths' => [
                $resolvedPath => 'PageBuilderUserBlocks',
            ],
        ]);
    }
}
