<?php declare(strict_types=1);

namespace Tkhamez\Slim\RoleAuth;

use Psr\Http\Message\ServerRequestInterface;

interface RoleProviderInterface
{
    /**
     * Returns roles from an authenticated user.
     *
     * Example: ['role.one', 'role.two']
     *
     * @param ServerRequestInterface $request
     * @return string[]
     */
    public function getRoles(ServerRequestInterface $request): array;
}
