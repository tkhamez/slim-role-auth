<?php

namespace Tkhamez\Slim\RoleAuth;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;

/**
 * Denies access to a route if the required role is missing.
 *
 * Sends an HTTP status 403 (default) or optionally a "Location"
 * header for a redirect.
 *
 * It loads the roles from a request attribute
 * (an array with string values, e.g. ['role.one', 'role.two']).
 *
 * The role attribute is provided by the RoleMiddleware class.
 *
 * The configured routes are compared against the route pattern from a
 * RouteInterface class from the request attribute named "route"
 * (provided by Slim).
 *
 * All routes are *allowed* if the "route" attribute is missing in the request object!
 */
class SecureRouteMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    /**
     * @var string[][]
     */
    private array $secured;

    /**
     * @var string[]
     */
    private array $options;

    /**
     * Constructor.
     *
     * Parameter secured:
     * - Example: ['/secured/public' => ['anonymous', 'user'], '/secured' => ['user']]
     * - First match will be used.
     * - Keys are route pattern, matched by "starts-with".
     * - Values are roles, only one must match to allow the route.
     *
     * Parameter options:
     * - Example: ['redirect_url' => '/login']
     * - redirect_url: send a Location header instead of a 403 status code.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param string[][] $secured
     * @param string[] $options
     */
    public function __construct(ResponseFactoryInterface $responseFactory, array $secured, array $options = [])
    {
        $this->responseFactory = $responseFactory;
        $this->secured = $secured;
        $this->options = $options;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = RouteContext::fromRequest($request)->getRoute();
        if (! $route instanceof RouteInterface) {
            return $handler->handle($request);
        }

        $roles = $request->getAttribute(RoleMiddleware::ROLES);
        $routePattern = $route->getPattern();

        $allowed = true;
        foreach ($this->secured as $securedRoute => $requiredRoles) {
            if (!str_starts_with($routePattern, $securedRoute)) {
                continue;
            }
            if (! is_array($roles) || count(array_intersect($requiredRoles, $roles)) === 0) {
                $allowed = false;
            }
            break;
        }

        if ($allowed === false) {
            $response = $this->responseFactory->createResponse();
            if (isset($this->options['redirect_url'])) {
                return $response
                    ->withHeader('Location', (string) $this->options['redirect_url'])
                    ->withStatus(302);
            } else {
                return $response->withStatus(403);
            }
        }

        return $handler->handle($request);
    }
}
