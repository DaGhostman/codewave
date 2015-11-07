<?php
namespace Wave\Framework\Interfaces\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

interface MiddlewareInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return bool
     */
    public function __invoke($request, $response, $next = null);
}
