<?php
namespace Wave\Framework\Interfaces\Middleware;

use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;

interface MiddlewareInterface
{
    public function before(RequestInterface $request);
    public function after(ResponseInterface $response);
}