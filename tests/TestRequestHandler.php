<?php

namespace Tkhamez\Slim\RoleAuth\Test;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class TestRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ServerRequestInterface
     */
    public $request;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return new Response();
    }
}
