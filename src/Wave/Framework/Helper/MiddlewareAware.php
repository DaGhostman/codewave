<?php
namespace Wave\Framework\Helper;

use Wave\Framework\Interfaces\Middleware\MiddlewareAwareInterface;
use Wave\Framework\Interfaces\Middleware\MiddlewareInterface;

abstract class MiddlewareAware implements MiddlewareAwareInterface
{
    /**
     * Adds middleware instances to the middleware stack
     *
     * @param \Wave\Framework\Interfaces\Middleware\MiddlewareInterface $middleware
     *
     * @return null
     */
    abstract public function addMiddleware(MiddlewareInterface $middleware);
}
