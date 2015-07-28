<?php
namespace Wave\Framework\Interfaces\Middleware;

use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;

interface MiddlewareInterface
{
    public function before(RequestInterface $request, ResponseInterface $response);
    public function after(ResponseInterface $response);
}
