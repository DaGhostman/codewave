<?php


namespace Stub;


use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;
use Wave\Framework\Interfaces\Middleware\MiddlewareInterface;

class StubMiddleware implements MiddlewareInterface
{
    public function before(RequestInterface $request, ResponseInterface $response)
    {
        echo 'Begin-';
    }
    public function after(ResponseInterface $response)
    {
        echo '-End';
    }
}