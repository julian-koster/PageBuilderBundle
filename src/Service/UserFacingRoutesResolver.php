<?php

namespace JulianKoster\PageBuilderBundle\Service;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

readonly class UserFacingRoutesResolver
{
    private string $routeName;

    public function __construct(
        private ContainerBagInterface $containerBag,
        private RouterInterface $router,
        string $routeName,
    )
    {
        $this->routeName = $routeName;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUserFacingRoutesArray(): array
    {
        if($this->containerBag->has('page_builder.user_facing_routes')) {
            return $this->containerBag->get('page_builder.user_facing_routes');
        }
        else {
            return [];
        }
    }

    public function verifyRoute(): string
    {
        try {
            return $this->router->generate($this->routeName);
        }
        catch (RouteNotFoundException $e) {
            throw new RouteNotFoundException($e->getMessage());
        }
    }
}