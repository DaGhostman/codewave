<?php

namespace Wave\Framework\Application;


use Wave\Framework\Event\Emitter;

class Wave
{
    protected $config;
    public static $_static = './static';
    public static $_public = './media';
    public static $_public_prefix = '/assets';

    protected $router = null;
    protected $dispatcher = null;
    protected $routes = null;

    /**
     * Creates the main application instance
     *
     * @param $config \Zend\Config\Reader Object containing the parsed configuration
     */
    public function __construct($config)
    {
        if (!$config instanceof \Zend\Config\Reader) {
            throw new \InvalidArgumentException("Invalid configuration");
        }

        $this->config = $config;

        Wave::$_public = $config['folders']['static'];
        Wave::$_static = $config['folders']['media'];

        Emitter::getInstance();
    }

    public function getRouter()
    {
        $router = null;
        if (null === $this->router) {
            $this->dispatcher = call_user_func(
                $this->config['routing']['dispatcher'],
                function(\FastRoute\RouteCollector $r) use (&$router) {
                    $router = $r;
                }
            );

            $this->router = $router;
        }

        return $this->router;
    }

    public function route($method, $pattern, $callback)
    {
        $this->router->addRoute($method, $pattern, $callback);
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
        $dispatcher = $this->dispatcher;
        $routeInfo = $dispatcher->dispatch($request->method(), $request->uri());
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                Emitter::getInstance()->trigger('route_notFound', array(
                    'method' => $request->method(),
                    'uri' => $request->uri(),
                    'data' => array('request' => $request)
                ));

                call_user_func($this->config['errorHandler']['NotFound'], $request);
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

                call_user_func($this->config['errorHandler']['NotAllowed'], $request, $allowedMethods);
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