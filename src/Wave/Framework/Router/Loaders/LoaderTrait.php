<?php

namespace Wave\Framework\Router\Loaders;


trait LoaderTrait
{

    public function getRaw()
    {
        return $this->raw;
    }

    public function pushRoutes($router)
    {
        if (!method_exists($router, 'addRoute')) {
            throw new \LogicException(
                'Provided router should have public method \'addRoute\''
            );
        }

        foreach ($this->raw as $route) {
            $router->addRoute($route->method, $route->uri, $route->callback);
        }
    }

    public function pushControllers($router)
    {
        if (!method_exists($router, 'controller')) {
            throw new \LogicException(
                'Provided router should have public method \'controller\''
            );
        }

        foreach ($this->raw as $route) {
            $router->controller($route['uri'], $route['class'], $route['filters'] ?: []);
        }
    }

    public function pushFilters($router)
    {
        if (!method_exists($router, 'filter')) {
            throw new \LogicException(
                'Provided router should have public method \'addRoute\''
            );
        }

        foreach ($this->raw as $filter) {
            $router->filter($filter['name'], $filter['callback']);
        }
    }
}
