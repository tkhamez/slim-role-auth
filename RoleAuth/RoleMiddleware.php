<?php declare(strict_types=1);

namespace Tkhamez\Slim\RoleAuth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;

/**
 * Adds roles to the request attribute "roles".
 *
 * Roles usually come from an authenticated user. It's an array
 * with string values, e. g. ['role.one', 'role.two'].
 *
 * Roles are loaded from a RoleProviderInterface object. If that does
 * not return any roles the 'anonymous' role is added.
 */
class RoleMiddleware
{
    /**
     * The anonymous role is added if the role provider does not return any roles.
     */
    const ROLE_ANONYMOUS = 'anonymous';

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
     * route_pattern: only authenticate for this routes, matched by "starts-with"
     *
     * Example:
     * ['route_pattern' => ['/secured']]
     *
     * @param RoleProviderInterface $roleService
     * @param array $options
     */
    public function __construct(RoleProviderInterface $roleService, array $options = [])
    {
        $this->roleService = $roleService;
        $this->options = $options;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next): ResponseInterface
    {
        if (! $this->shouldAuthorize($request->getAttribute('route'))) {
            return $next($request, $response);
        }

        $roles = $this->roleService->getRoles($request);
        if (count($roles) === 0) {
            // no authenticated roles, add anonymous role
            $roles[] = self::ROLE_ANONYMOUS;
        }

        $request = $request->withAttribute('roles', $roles);

        return $next($request, $response);
    }

    private function shouldAuthorize(RouteInterface $route = null)
    {
        if (isset($this->options['route_pattern']) && is_array($this->options['route_pattern']) &&
            count($this->options['route_pattern']) > 0 && $route !== null
        ) {
            $routePattern = $route->getPattern();
            foreach ($this->options['route_pattern'] as $includePattern) {
                if (strpos($routePattern, $includePattern) === 0) {
                    return true;
                }
            }
        }
        return false;
    }
}
