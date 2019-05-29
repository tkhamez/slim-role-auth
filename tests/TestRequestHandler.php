<?php

class TestRequestHandler implements Psr\Http\Server\RequestHandlerInterface
{
    public $request;

    public function handle(Psr\Http\Message\ServerRequestInterface $request): Psr\Http\Message\ResponseInterface
    {
        $this->request = $request;
        return new \TestResponse();
    }
}
