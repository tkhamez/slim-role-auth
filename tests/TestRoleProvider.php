<?php
namespace Test;

class TestRoleProvider implements \Tkhamez\Slim\RoleAuth\RoleProviderInterface
{
    private $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function getRoles(\Psr\Http\Message\ServerRequestInterface $request): array
    {
        return $this->roles;
    }
}
