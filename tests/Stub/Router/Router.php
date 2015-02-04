<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 02/02/15
 * Time: 23:39
 */

namespace Stub\Router;


class Router
{
    protected $routes = [];

    public function addRoute()
    {
        $this->routes[] = func_get_args();
    }

    public function getArray()
    {
        return $this->routes;
    }
}
