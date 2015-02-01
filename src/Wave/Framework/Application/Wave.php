<?php

namespace Wave\Framework\Application;


use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\RouteCollector;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Wave\Framework\Event\Emitter;

class Wave implements LoggerAwareInterface
{
    protected $config;
    protected $logger = null;
    protected $response = null;

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

        $this->router = new RouteCollector();
        Emitter::getInstance();
    }

    public function route($method, $pattern, $callback)
    {
        $this->router->addRoute($method, $pattern, $callback);
    }

    public function __call($name, $args)
    {
        switch (strtolower($name)) {
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
            case 'any':
                $this->router->any($args[0], $args[1]);
                break;
        }
    }

    public function run($request)
    {
        Emitter::getInstance()->trigger('request');

        $dispatcher = new Dispatcher($this->router->getData());

        try {
            $this->response = $dispatcher->dispatch(
                $request->method(),
                $request->uri()
            );

        } catch (Phroute\Phroute\Exception\HttpRouteNotFoundException $e) {
            // TODO: Invoke handler
        } catch (Phroute\Phroute\Exception\HttpMethodNotAllowedException $e) {
            // @TODO: Invoke Handler
        }


    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __destruct()
    {
        Emitter::getInstance()->trigger('render');
    }
}
