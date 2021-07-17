<?php

namespace Tkhamez\Slim\RoleAuth\Test;

use Psr\Http\Message\ServerRequestInterface;
use Tkhamez\Slim\RoleAuth\RoleProviderInterface;

class TestRoleProvider implements RoleProviderInterface
{
    /**
     * @var string[]
     */
    private $roles;

    /**
     * @param string[] $roles
     */
    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function getRoles(ServerRequestInterface $request): array
    {
        return $this->roles;
    }
}
