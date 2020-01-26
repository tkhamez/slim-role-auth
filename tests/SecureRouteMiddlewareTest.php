<?php

namespace Tkhamez\Slim\RoleAuth\Test;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tkhamez\Slim\RoleAuth\RoleMiddleware;
use Tkhamez\Slim\RoleAuth\SecureRouteMiddleware;

class SecureRouteMiddlewareTest extends TestCase
{
    public function testAllowProtectedWithoutRoute()
    {
        $conf = ['/secured' => ['role1']];
        $response = $this->invokeMiddleware($conf, ['role2']);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDenyProtectedWithoutRole()
    {
        $conf = ['/secured' => ['role1']];
        $response = $this->invokeMiddleware($conf, null, '/secured');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testDenyProtectedWrongRole()
    {
        $conf = ['/secured' => ['role1', 'role2']];
        $response = $this->invokeMiddleware($conf, ['role3', 'role4'], '/secured');

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testAllowProtected()
    {
        $conf = ['/secured' => ['role1', 'role2']];
        $response = $this->invokeMiddleware($conf, ['role2', 'role3'], '/secured');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPathMatchesStartsWith()
    {
        $conf = ['/p1' => ['role1']];
        $response = $this->invokeMiddleware($conf, ['role1'], '/p1/p2');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testMatchesFirstFoundPath()
    {
        $conf = ['/p0' => ['role0'], '/p1' => ['role1'], '/p1/p2' => ['role2']];
        $response = $this->invokeMiddleware($conf, ['role1'], '/p1/p2');
        $this->assertSame(200, $response->getStatusCode());

        $conf = ['/p0' => ['role0'], '/p1/p2' => ['role2'], '/p1' => ['role1']];
        $response = $this->invokeMiddleware($conf, ['role1'], '/p1/p2');
        $this->assertSame(403, $response->getStatusCode());
    }

    public function testRedirects()
    {
        $conf = ['/secured' => ['role']];
        $opts = ['redirect_url' => '/login'];
        $response = $this->invokeMiddleware($conf, [], '/secured', $opts);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/login', $response->getHeader('Location')[0]);
    }

    private function invokeMiddleware($conf, $roles, $path = null, $opts = []): ResponseInterface
    {
        $request = (new ServerRequestFactory)->createServerRequest('GET', '/');
        $request = $this->addRouteContext($request, $path);
        $request = $request->withAttribute(RoleMiddleware::ROLES, $roles);

        $sec = new SecureRouteMiddleware(new ResponseFactory(), $conf, $opts);

        return $sec->process($request, new TestRequestHandler());
    }
}
