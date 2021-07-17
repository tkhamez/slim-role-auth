<?php

namespace Tkhamez\Slim\RoleAuth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;

/**
 * Adds roles to a request attribute.
 *
 * Roles usually come from an authenticated user. It's an array
 * with string values, e. g. ['role.one', 'role.two'].
 *
 * Roles are loaded from a RoleProviderInterface object.
 */
class RoleMiddleware implements MiddlewareInterface
{
    public const ROLES = 'slim_role_auth__roles';

    /**
     * @var RoleProviderInterface
     */
    private $roleService;

    /**
     * @var string[][]
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
     * @param string[][] $options
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
        if ($this->shouldAddRoles(RouteContext::fromRequest($request)->getRoute())) {
            $request = $request->withAttribute(self::ROLES, $this->roleService->getRoles($request));
        }

        return $handler->handle($request);
    }

    /**
     * @param RouteInterface|null $route
     * @return bool
     */
    private function shouldAddRoles(RouteInterface $route = null): bool
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
}
