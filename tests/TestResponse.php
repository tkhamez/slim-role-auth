<?php

class TestResponse implements Psr\Http\Message\ResponseInterface
{
    private $headers = [];

    private $statusCode = 200;

    public function getProtocolVersion()
    {
    }

    public function withProtocolVersion($version)
    {
    }

    public function getHeaders()
    {
    }

    public function hasHeader($name)
    {
    }

    public function getHeader($name)
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaderLine($name)
    {
    }

    public function withHeader($name, $value)
    {
        $response = clone $this;
        $response->headers[$name][] = $value;
        return $response;
    }

    public function withAddedHeader($name, $value)
    {
    }

    public function withoutHeader($name)
    {
    }

    public function getBody()
    {
    }

    public function withBody(Psr\Http\Message\StreamInterface $body)
    {
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $response = clone $this;
        $response->statusCode = $code;
        return $response;
    }

    public function getReasonPhrase()
    {
    }
}
