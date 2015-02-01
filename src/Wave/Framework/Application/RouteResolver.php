<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 01/02/15
 * Time: 14:17
 */

namespace Wave\Framework\Application;

use Phroute\Phroute\HandlerResolverInterface;

class RouteResolver implements HandlerResolverInterface
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
        if (is_array($handler) and is_string($handler[0])) {
            $handler[0] = $this->container[$handler[0]];
        }

        return $handler;
    }
}
