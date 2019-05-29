<?php

class TestResponseFactory implements Psr\Http\Message\ResponseFactoryInterface
{
    public function createResponse(int $code = 200, string $reasonPhrase = ''): Psr\Http\Message\ResponseInterface
    {
        return new \TestResponse();
    }
}
