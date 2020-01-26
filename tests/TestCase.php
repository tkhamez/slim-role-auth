<?php

namespace Tkhamez\Slim\RoleAuth\Test;

use Psr\Http\Message\ServerRequestInterface;
use Slim\CallableResolver;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\Dispatcher;
use Slim\Routing\RouteCollector;
use Slim\Routing\RoutingResults;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function addRouteContext(ServerRequestInterface $request, string $path = null): ServerRequestInterface
    {
        $routeParser = $this->getMockBuilder(RouteParserInterface::class)->getMock();
        $request = $request->withAttribute('routeParser', $routeParser);

        $routingResults = new RoutingResults(
            new Dispatcher(new RouteCollector(new ResponseFactory(),new CallableResolver())),
            'GET',
            '/',
            200
        );
        $request = $request->withAttribute('routingResults', $routingResults);

        if ($path) {
            $route = $this->getMockBuilder(RouteInterface::class)->getMock();
            $route->method('getPattern')->willReturn($path);
            $request = $request->withAttribute('route', $route);
        }

        return $request;
    }
}
