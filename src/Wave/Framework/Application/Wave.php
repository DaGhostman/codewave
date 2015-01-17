<?php

namespace Wave\Framework\Application;


use Wave\Framework\Event\Emitter;

class Wave
{
    protected $config;

    protected $routes = array();

    /**
     * Creates the main application instance
     *
     * @param $config array Array from \Zend\Config\Reader containing the
     *                parsed configuration
     * @throws \InvalidArgumentException
     */
    public function __construct($config)
    {
        if (!is_array($config)) {
            throw new \InvalidArgumentException("Invalid configuration");
        }

        $this->config = $config;

        Emitter::getInstance();
    }

    public function route($method, $pattern, $callback)
    {
        array_push($this->routes, array($method, $pattern, $callback));
        #$this->router->addRoute($method, $pattern, $callback);
    }

    public function __call($name, $args)
    {
        switch (strtolower($name)):
            case 'get':
            case 'post':
            case 'put':
            case 'delete':
            case 'head':
            case 'options':
            case 'trace':
            case 'cli':
               $this->route(strtoupper($name), $args[0], $args[1]);
               break;
        endswitch;
    }

    public function run($request)
    {
        $routes = $this->routes;
        $dispatcher = call_user_func(
            $this->config['routing']['dispatcher'],
            function (\FastRoute\RouteCollector $r) use (&$routes) {
                foreach ($routes as $route) {
                    $r->addRoute($route[0], $route[1], $route[2]);
                }
            },
            array(
                'cacheFile' => $this->config['folders']['cache'] . DIRECTORY_SEPARATOR . $this->config['routing']['cache'], /* required */
                'cacheDisabled' => false
            )
        );


        $routeInfo = $dispatcher->dispatch($request->method(), $request->uri());
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                Emitter::getInstance()->trigger('route_notFound', array(
                    'uri' => $request->uri(),
                    'data' => array('request' => $request)
                ));

                call_user_func($this->config['errorHandler']['notFound'], $request);
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];

                Emitter::getInstance()->trigger('route_badMethod', array(
                    'method' => $request->method(),
                    'uri' => $request->uri(),
                    'data' => array(
                        'request' => $request,
                        'methodsAllowed' => $allowedMethods
                    )
                ));

                call_user_func($this->config['errorHandler']['notAllowed'], $request, $allowedMethods);
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $request->setVariables($routeInfo[2]);

                Emitter::getInstance()->trigger('route_called', array(
                    'method' => $request->method(),
                    'uri' => $request->uri(),
                    'data' => array(
                        'handler' => $handler,
                        'request' => $request,
                        'vars' => $routeInfo[2]
                    )
                ));

                call_user_func($handler, $request);
                break;
        }
    }
}
