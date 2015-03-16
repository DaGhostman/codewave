<?php
namespace Wave\Framework\Router;

use Phroute\Phroute\HandlerResolverInterface;

class Resolver implements HandlerResolverInterface
{

    private $container = null;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function resolve($handler)
    {
        if (is_array($handler) &&
            is_string($handler[0]) &&
            isset($this->container[$handler[0]])) {
                $handler[0] = $this->container[$handler[0]];
        }
        
        return $handler;
    }
}
