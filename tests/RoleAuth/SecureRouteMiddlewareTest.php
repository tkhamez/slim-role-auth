<?php

namespace Tkhamez\Tests\Slim\RoleAuth;

use Tkhamez\Slim\RoleAuth\SecureRouteMiddleware;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouteInterface;

class SecureRouteMiddlewareTest extends \PHPUnit\Framework\TestCase
{
    public function testAllowProtectedWithoutRoute()
    {
        $conf = ['/secured' => ['role1']];
        $response = $this->invokeMiddleware($conf, '/secured', ['role2'], false);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDenyProtectedWithoutRole()
    {
        $conf = ['/secured' => ['role1']];
        $response = $this->invokeMiddleware($conf, '/secured', null, true);

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testDenyProtectedWrongRole()
    {
        $conf = ['/secured' => ['role1', 'role2']];
        $response = $this->invokeMiddleware($conf, '/secured', ['role3', 'role4'], true);

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testAllowProtected()
    {
        $conf = ['/secured' => ['role1', 'role2']];
        $response = $this->invokeMiddleware($conf, '/secured', ['role2', 'role3'], true);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPathMatchesStartsWith()
    {
        $conf = ['/p1' => ['role1']];
        $response = $this->invokeMiddleware($conf, '/p1/p2', ['role1'], true);
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testMatchesFirstFoundPath()
    {
        $conf = ['/p1' => ['role1'], '/p1/p2' => ['role2']];
        $response = $this->invokeMiddleware($conf, '/p1/p2', ['role1'], true);
        $this->assertSame(200, $response->getStatusCode());

        $conf = ['/p1/p2' => ['role2'], '/p1' => ['role1']];
        $response = $this->invokeMiddleware($conf, '/p1/p2', ['role1'], true);
        $this->assertSame(403, $response->getStatusCode());
    }

    public function testRedirects()
    {
        $conf = ['/secured' => ['role']];
        $opts = ['redirect_url' => '/login'];
        $response = $this->invokeMiddleware($conf, '/secured', [], true, $opts);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/login', $response->getHeader('Location')[0]);
    }

    /**
     * @param $conf
     * @param $path
     * @param $roles
     * @param $addRoute
     * @param array $opts
     * @return Response
     */
    private function invokeMiddleware($conf, $path, $roles, $addRoute, $opts = [])
    {
        $route = $this->getMockBuilder(RouteInterface::class)->getMock();
        $route->method('getPattern')->willReturn($path);

        $request = Request::createFromEnvironment(Environment::mock());
        if ($addRoute) {
            $request = $request->withAttribute('route', $route);
        }
        $request = $request->withAttribute('roles', $roles);

        $sec = new SecureRouteMiddleware($conf, $opts);

        $next = function($request, $response) {
            return $response;
        };

        return $sec($request, new Response(), $next);
    }
}
