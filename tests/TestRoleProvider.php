<?php
namespace Test;

use Psr\Http\Message\ServerRequestInterface;
use Tkhamez\Slim\RoleAuth\RoleProviderInterface;

class TestRoleProvider implements RoleProviderInterface
{
    private $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function getRoles(ServerRequestInterface $request): array
    {
        return $this->roles;
    }
}
