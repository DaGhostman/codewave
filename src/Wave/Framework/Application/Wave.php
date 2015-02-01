<?php

namespace Wave\Framework\Application;


use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Wave\Framework\Event\Emitter;

class Wave implements LoggerAwareInterface
{
    protected $config;
    protected $logger = null;
    protected $response = null;

    protected $notFound = null;
    protected $notAllowed = null;

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
            $response = $dispatcher->dispatch(
                $request->method(),
                $request->uri()
            );

            echo $response;
        } catch (HttpRouteNotFoundException $e) {
            call_user_func($this->notFound, $e);
        } catch (HttpMethodNotAllowedException $e) {
            call_user_func($this->notAllowed, $e);
        }
    }

    public function setNotFoundHandler($f)
    {
        $this->notFound = $f;
    }

    public function setNotAllowedHandler($f)
    {
        $this->notAllowed = $f;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
