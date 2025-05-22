<?php

namespace JulianKoster\PageBuilderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigValidator
{
    public static function validate(array $config, ContainerBuilder $builder): void
    {
        $resolved = $builder->getParameterBag()->resolveValue($config['template_dir']);

        if (!\is_dir($resolved)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid page_builder.template_dir "%s": directory does not exist. Did you forget to create the directory? If created, make sure it\'s path matches with the path configured under page_builder.template_dir in the PageBuilderBundle\'s configuration file.',
                $resolved
            ));
        }

        // Other checks for config
    }
}