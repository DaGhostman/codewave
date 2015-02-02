<?php

namespace Wave\Framework\Application;

use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Wave\Framework\Event\Emitter;
use Wave\Framework\Http\Request;

/**
 * Class Wave
 * @package Wave\Framework\Application
 */
class Wave implements LoggerAwareInterface
{
    protected $config;
    protected $response = null;
    protected $logger = null;
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
        if (!is_null($container)) {
            $container['router'] = function () use ($router) {
                return $router;
            };
        }
    }

    /**
     * Proxies all method calls to the PHRoute instance
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
     * @param Request $request
     */
    public function run(Request $request)
    {
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
            /**
             * @codeCoverageIgnore
             */
            if ($this->logger) {
                $this->logger->err($e->getMessage(), $e);
            }

            $this->notFound ? call_user_func($this->notFound, $e) : null;
        } catch (HttpMethodNotAllowedException $e) {
            /**
             * @codeCoverageIgnore
             */
            if ($this->logger) {
                $this->logger->err($e->getMessage(), $e);
            }

            $this->notAllowed ? call_user_func($this->notAllowed, $e) : null;
        }
    }

    /**
     * Defines the callback to trigger on 404 error
     *
     * @param $func callable
     */
    public function setNotFoundHandler(callable $func)
    {
        $this->notFound = $func;
    }

    /**
     * Defines the callback to fire, when request
     * method is not allowed.
     *
     * @param $func callable
     */
    public function setNotAllowedHandler(callable $func)
    {
        $this->notAllowed = $func;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
