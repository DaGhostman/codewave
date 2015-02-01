<?php

namespace Wave\Framework\Application;

use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Wave\Framework\Event\Emitter;

/**
 * Class Wave
 * @package Wave\Framework\Application
 */
class Wave implements LoggerAwareInterface
{
    protected $config;
    protected $logger = null;
    protected $response = null;

    protected $notFound = null;
    protected $notAllowed = null;

    protected $container = null;

    /**
     * Creates the main application instance
     *
     * @param $config array A placeholder for future use
     * @param $container mixed Instance of Pimple
     * @throws \InvalidArgumentException
     */
    public function __construct($container, $config = [])
    {
        if (!is_array($config)) {
            throw new \InvalidArgumentException("Invalid configuration");
        }
        $this->container = $container;
        $this->router = new RouteCollector();

        $router = $this->router;
        if (!empty($container)) {
            $container['router'] = function () use ($router) {
                return $router;
            };
        }
        Emitter::getInstance();
    }

    /**
     * Provides direct access to PHRoutes::addRoute
     *
     * @param $method
     * @param $pattern
     * @param $callback
     */
    public function route($method, $pattern, $callback)
    {
        $this->router->addRoute($method, $pattern, $callback);
    }

    /**
     * Allows access to the methods of PHRoute
     *
     * @param $name
     * @param $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array([$this->router, $name], $args);
    }

    /**
     * Starts the application routing.
     * Second argument is passed directly to the dispatcher. See
     *
     * @param Wave\Framework\Http\Request $request
     */
    public function run($request)
    {
        Emitter::getInstance()->trigger('request');

        $dispatcher = new Dispatcher(
            $this->router->getData(),
            new RouteResolver($this->container)
        );

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
