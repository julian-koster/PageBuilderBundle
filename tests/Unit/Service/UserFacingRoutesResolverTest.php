<?php

namespace Service;

use JulianKoster\PageBuilderBundle\Service\UserFacingRoutesResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class UserFacingRoutesResolverTest extends TestCase
{
    public function testRouteResolverVerifyRoute()
    {
        $routeName = "app_homepage";

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->with('app_homepage')->willReturn('/');

        $containerBag = $this->createMock(ContainerBagInterface::class);

        $resolver = new UserFacingRoutesResolver($containerBag, $router, $routeName);
        $url = $resolver->verifyRoute();

        $this->assertEquals('/', $url);
    }

    public function testVerifyRouteThrowsRuntimeException()
    {
        $this->expectException(RouteNotFoundException::class);

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')
            ->with('non_existent_route')
            ->willThrowException(new RouteNotFoundException('Route not found'));

        $containerBag = $this->createMock(ContainerBagInterface::class);

        $resolver = new UserFacingRoutesResolver($containerBag, $router, 'non_existent_route');

        $resolver->verifyRoute();
    }

    public function testUserFacingRoutesArrayIsArray()
    {
        $containerBag = $this->createMock(ContainerBagInterface::class);
        $router = $this->createMock(RouterInterface::class);
        $resolver = new UserFacingRoutesResolver($containerBag, $router, 'app_homepage');
        self::assertIsArray($resolver->getUserFacingRoutesArray());
    }

    public function testUserFacingRoutesArrayContainsValidKeys()
    {

    }
}