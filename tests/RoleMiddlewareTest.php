<?php

namespace Tkhamez\Slim\RoleAuth\Test;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tkhamez\Slim\RoleAuth\RoleMiddleware;

class RoleMiddlewareTest extends TestCase
{
    public function testAddsRolesForPaths(): void
    {
        $request1 = $this->invokeMiddleware(['/path1', '/path2'], ['r1', 'r2'], '/path1');
        $request2 = $this->invokeMiddleware(['/path1', '/path2'], ['r1', 'r2'], '/path23/4');

        $this->assertSame(['r1', 'r2'], $request1->getAttribute(RoleMiddleware::ROLES));
        $this->assertSame(['r1', 'r2'], $request2->getAttribute(RoleMiddleware::ROLES));
    }

    public function testDoesNotAddRolesForOtherPaths(): void
    {
        $request1 = $this->invokeMiddleware(['/path1'], ['role1'], '/other/path');
        $request2 = $this->invokeMiddleware(['/path1'], ['role1'], '/not/path1');

        $this->assertNull($request1->getAttribute(RoleMiddleware::ROLES));
        $this->assertNull($request2->getAttribute(RoleMiddleware::ROLES));
    }

    public function testAddsRolesWithoutPaths(): void
    {
        $request1 = $this->invokeMiddleware([], ['role1'], '/path1');
        $request2 = $this->invokeMiddleware([], ['role1'], '/path1');

        $this->assertSame(['role1'], $request1->getAttribute(RoleMiddleware::ROLES));
        $this->assertSame(['role1'], $request2->getAttribute(RoleMiddleware::ROLES));
    }

    public function testAddsRolesWithoutRouteAttribute(): void
    {
        $request = $this->invokeMiddleware(['/path1'], ['role1']);

        $this->assertSame(['role1'], $request->getAttribute(RoleMiddleware::ROLES));
    }

    /**
     * @param string[] $routes
     * @param string[] $roles
     */
    private function invokeMiddleware(array $routes, array $roles, string $path = null): ServerRequestInterface
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $request = $this->addRouteContext($request, $path);

        $roleProvider = new TestRoleProvider($roles);
        $roleMiddleware = new RoleMiddleware($roleProvider, ['route_pattern' =>  $routes]);

        $requestHandler = new TestRequestHandler();
        $roleMiddleware->process($request, $requestHandler);

        return $requestHandler->request;
    }
}
