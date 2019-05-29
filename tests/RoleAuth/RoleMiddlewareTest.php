<?php

namespace Tkhamez\Tests\Slim\RoleAuth;

use PHPUnit\Framework\TestCase;
use Tkhamez\Slim\RoleAuth\RoleMiddleware;
use Tkhamez\Slim\RoleAuth\RoleProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;

class RoleMiddlewareTest extends TestCase
{
    public function testAddsRolesForPaths()
    {
        $request1 = $this->invokeMiddleware('/path1', ['/path1', '/path2'], ['r1', 'r2'], true);
        $request2 = $this->invokeMiddleware('/path23/4', ['/path1', '/path2'], ['r1', 'r2'], true);

        $this->assertSame(['r1', 'r2'], $request1->getAttribute('roles'));
        $this->assertSame(['r1', 'r2'], $request2->getAttribute('roles'));
    }

    public function testDoesNotAddRolesForOtherPaths()
    {
        $request1 = $this->invokeMiddleware('/other/path', ['/path1'], ['role1'], true);
        $request2 = $this->invokeMiddleware('/not/path1', ['/path1'], ['role1'], true);

        $this->assertNull($request1->getAttribute('roles'));
        $this->assertNull($request2->getAttribute('roles'));
    }

    public function testAddsRolesWithoutPaths()
    {
        $request1 = $this->invokeMiddleware('/path1', null, ['role1'], true);
        $request2 = $this->invokeMiddleware('/path1', [], ['role1'], true);

        $this->assertSame(['role1'], $request1->getAttribute('roles'));
        $this->assertSame(['role1'], $request2->getAttribute('roles'));
    }

    public function testAddsRolesWithoutRouteAttribute()
    {
        $request = $this->invokeMiddleware('/path1', ['/path1'], ['role1'], false);

        $this->assertSame(['role1'], $request->getAttribute('roles'));
    }

    private function invokeMiddleware($path, $routes, $roles, $addRoute): ServerRequestInterface
    {
        $request = new \TestRequest();
        $requestHandler = new \TestRequestHandler();

        if ($addRoute) {
            $route = $this->getMockBuilder(RouteInterface::class)->getMock();
            $route->method('getPattern')->willReturn($path);
            $request = $request->withAttribute('route', $route);
        }

        /* @var $roleProvider RoleProviderInterface|\PHPUnit_Framework_MockObject_MockObject */
        $roleProvider = $this->getMockBuilder(RoleProviderInterface::class)->getMock();
        $roleProvider->method('getRoles')->willReturn($roles);

        $roleMiddleware = new RoleMiddleware($roleProvider, ['route_pattern' =>  $routes]);

        $roleMiddleware->process($request, $requestHandler);
        return $requestHandler->request;
    }
}
