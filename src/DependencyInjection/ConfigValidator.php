<?php

namespace JulianKoster\PageBuilderBundle\DependencyInjection;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigValidator
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function validate(array $config, ContainerBuilder $builder): void
    {
        $resolvedTemplateDir = $builder->getParameterBag()->resolveValue($config['template_dir']);

        if (!\is_dir($resolvedTemplateDir)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid page_builder.template_dir "%s": directory does not exist. Did you forget to create the directory? If created, make sure it\'s path matches with the path configured under page_builder.template_dir in the PageBuilderBundle\'s configuration file.',
                $resolvedTemplateDir
            ));
        }

        $resolvedImageDir = $builder->getParameterBag()->resolveValue($config['image_dir']);

        # We're not going to throw an actual exception as it seems silly to halt the entire build process over an image upload dir.
        if (!\is_dir($resolvedImageDir)) {
            $this->logger->warning(sprintf(
                'Invalid page_builder.image_dir "%s": directory does not exist. Did you forget to create the directory? If created, make sure it\'s path matches with the path configured under page_builder.image_dir in the PageBuilderBundle\'s configuration file.',
                $resolvedImageDir
            ));
        }

        $userFacingRoutes = $builder->getParameterBag()->resolveValue($config['user_facing_routes']);
        if (!\is_array($userFacingRoutes)) {
            throw new \InvalidArgumentException('PageBuilderBundle\'s config setting: user_facing_routes should be an array.');
        }

        $adminRoles = $builder->getParameterBag()->resolveValue($config['admin_roles']);
        if (!\is_array($adminRoles)) {
            throw new \InvalidArgumentException('PageBuilderBundle\'s config setting: admin_roles should be an array.');
        }

        $allowAnonymousPreviews = $builder->getParameterBag()->resolveValue($config['allow_anonymous_previews']);
        if (!\is_bool($allowAnonymousPreviews)) {
            throw new \InvalidArgumentException('PageBuilderBundle\'s config setting: allow_anonymous_previews should be a boolean.');
        }
    }
}