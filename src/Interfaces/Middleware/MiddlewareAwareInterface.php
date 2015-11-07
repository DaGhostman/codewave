<?php
namespace Wave\Framework\Interfaces\Middleware;

interface MiddlewareAwareInterface
{
    /**
     * Adds middleware instances to the middleware stack
     *
     * @param \Wave\Framework\Interfaces\Middleware\MiddlewareInterface $middleware
     *
     * @return null
     */
    public function addMiddleware(\SplDoublyLinkedList $middleware);
}
