<?php

namespace Tkhamez\Slim\RoleAuth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;

/**
 * Denies access to a route if the required role is missing (403).
 *
 * It loads the roles from the request attribute named "roles"
 * (an array with string values, e. g. ['role.one', 'role.two']).
 *
 * The role attribute is provided by the RoleMiddleware class.
 *
 * The configured routes are compared against the route pattern from a
 * RouteInterface class from the request attribute named "route"
 * (provided by Slim).
 *
 * If the roles or the route attribute is missing, all routes are allowed.
 */
class SecureRouteMiddleware
{
    /**
     * @var array
     */
    private $secured;

    /**
     * Constructor.
     *
     * First match will be used.
     *
     * Keys are route pattern, matched by "starts-with".
     * Values are roles, only one must match to allow the route.
     *
     * Example:
     * [
     *      '/secured/public' => ['anonymous', 'user'],
     *      '/secured' => ['user'],
     * ]
     *
     * @param array $secured
     */
    public function __construct(array $secured)
    {
        $this->secured = $secured;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $roles = $request->getAttribute('roles');

        $route = $request->getAttribute('route');
        if (! $route instanceof RouteInterface) {
            return $next($request, $response);
        }

        $allowed = null;

        $routePattern = $route->getPattern();
        foreach ($this->secured as $securedRoute => $requiredRoles) {
            if (strpos($routePattern, $securedRoute) === 0) {
                $allowed = false;
                if (is_array($roles) && count(array_intersect($requiredRoles, $roles)) > 0) {
                    $allowed = true;
                }
                break;
            }
        }

        if ($allowed === false) {
            return $response->withStatus(403);
        }

        return $next($request, $response);
    }
}
