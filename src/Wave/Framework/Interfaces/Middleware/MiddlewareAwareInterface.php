<?php
namespace Wave\Framework\Interfaces\Middleware;

interface MiddlewareAwareInterface
{
    /**
     * Adds middleware instances to the stack and of middlewares
     *
     * @param \Wave\Framework\Interfaces\Middleware\MiddlewareInterface $middleware
     *
     * @return null
     */
    public function addMiddleware(MiddlewareInterface $middleware);
}
