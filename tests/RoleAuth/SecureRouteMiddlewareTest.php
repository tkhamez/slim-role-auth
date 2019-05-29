<?php

namespace Tkhamez\Tests\Slim\RoleAuth;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Tkhamez\Slim\RoleAuth\SecureRouteMiddleware;
use Slim\Interfaces\RouteInterface;

class SecureRouteMiddlewareTest extends TestCase
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
        $conf = ['/p0' => ['role0'], '/p1' => ['role1'], '/p1/p2' => ['role2']];
        $response = $this->invokeMiddleware($conf, '/p1/p2', ['role1'], true);
        $this->assertSame(200, $response->getStatusCode());

        $conf = ['/p0' => ['role0'], '/p1/p2' => ['role2'], '/p1' => ['role1']];
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

    private function invokeMiddleware($conf, $path, $roles, $addRoute, $opts = []): ResponseInterface
    {
        $request = new \TestRequest();
        $responseFactory = new \TestResponseFactory();
        $requestHandler = new \TestRequestHandler();

        if ($addRoute) {
            $route = $this->getMockBuilder(RouteInterface::class)->getMock();
            $route->method('getPattern')->willReturn($path);
            $request = $request->withAttribute('route', $route);
        }
        $request = $request->withAttribute('roles', $roles);

        $sec = new SecureRouteMiddleware($responseFactory, $conf, $opts);

        return $sec->process($request, $requestHandler);
    }
}
