<?php

use Symfony\Component\Routing\RouterInterface;

class MainBuilderControllerTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    public function testMainBuilderControllerOpensPage()
    {
        $client = static::createClient();
        $router = static::getContainer()->get(RouterInterface::class);

        $route = $router->generate('juliankoster_pagebuilder_mainbuilder_render_main_view_index');

        $methods = $route->getMethods();
        $path = $route->getPath();

        $this->assertContains("GET", $methods);

        $client->request('GET', $path);
        $status = $client->getResponse()->getStatusCode();

        $this->assertEquals(200, $status);
    }
}