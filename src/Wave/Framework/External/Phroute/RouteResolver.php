<?php
/**
 * Created by PhpStorm.
 * User: elham_asmar
 * Date: 01/04/2015
 * Time: 16:18
 */

namespace Wave\Framework\External\Phroute;


use Phroute\Phroute\HandlerResolverInterface;

class RouteResolver implements HandlerResolverInterface{

    protected $resolver;

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }


    /**
     * Create an instance of the given handler.
     *
     * @param $handler
     * @return array
     */
    public function resolve($handler)
    {
        if (is_array($handler) && is_string($handler[0])) {
            $handler[0] = $this->resolver->reolve($handler[0]);
        }

        return $handler;
    }
}