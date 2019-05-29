<?php

namespace Tkhamez\Slim\RoleAuth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteInterface;

/**
 * Adds roles to the request attribute "roles".
 *
 * Roles usually come from an authenticated user. It's an array
 * with string values, e. g. ['role.one', 'role.two'].
 *
 * Roles are loaded from a RoleProviderInterface object.
 */
class RoleMiddleware implements MiddlewareInterface
{
    /**
     * @var RoleProviderInterface
     */
    private $roleService;

    /**
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * Available options (all optional):
     * - route_pattern: only add roles for this routes, matched by "starts-with". If omitted the roles are always added.
     *
     * The option "route_pattern" is ignored if the "route" attribute is missing in the request object, so roles
     * are always added if the "route" attribute is missing.
     *
     * Example: ['route_pattern' => ['/secured']]
     *
     * @param RoleProviderInterface $roleService
     * @param array $options
     */
    public function __construct(RoleProviderInterface $roleService, array $options = [])
    {
        $this->roleService = $roleService;
        $this->options = $options;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->shouldAddRoles($request->getAttribute('route'))) {
            $request = $request->withAttribute('roles', $this->getRoles($request));
        }

        return $handler->handle($request);
    }

    /**
     * @param RouteInterface|null $route
     * @return bool
     */
    private function shouldAddRoles(RouteInterface $route = null)
    {
        if ($route === null) {
            return true;
        }

        if (isset($this->options['route_pattern']) &&
            is_array($this->options['route_pattern']) &&
            count($this->options['route_pattern']) > 0
        ) {
            $routePattern = $route->getPattern();
            foreach ($this->options['route_pattern'] as $includePattern) {
                if (strpos($routePattern, $includePattern) === 0) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * @param ServerRequestInterface $request
     * @return array|string[]
     */
    private function getRoles(ServerRequestInterface $request)
    {
        $roles = $this->roleService->getRoles($request);

        return is_array($roles) ? $roles : [];
    }
}
