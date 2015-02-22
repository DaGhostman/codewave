<?php
namespace Wave\Framework\Application;

use Wave\Framework\Router\Dispatcher;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Wave\Framework\Http\Server\Request;
use Wave\Framework\Router\Resolver;
use Wave\Framework\Http\Server\Response;
use Psr\Http\Message\ResponseInterface;
use Wave\Framework\Application\Server;

/**
 * Class Wave
 *
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

    protected $dispatcher = null;

    protected $router = null;

    /**
     * Creates the main application instance
     *
     * @param $config array
     *            A placeholder for future use
     * @param $container mixed
     *            Instance of Pimple
     * @throws \InvalidArgumentException
     */
    public function __construct($container, $config = [])
    {
        if (! is_array($config)) {
            throw new \InvalidArgumentException("Invalid configuration");
        }
        $this->container = $container;
        $this->router = new RouteCollector();

        $router = $this->router;
        if (! is_null($container)) {
            $container['router'] = function () use ($router) {
                return $router;
            };
        }

        $this->dispatcher = function($router, $container) {
            new Dispatcher(
                $this->router->getData(),
                new Resolver($this->container)
            );
        };
    }

    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Proxies all method calls to the PHRoute instance
     *
     * @param
     *            $name
     * @param
     *            $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array([
            $this->router,
            $name
        ], $args);
    }

    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * Starts the application routing.
     * Second argument is passed directly to the dispatcher. See
     *
     * @param Request $request
     */
    public function run(Request $request, $response = null, $server = null)
    {

        if (!is_null($response) && !$response instanceof ResponseInterface) {
            throw new \InvalidArgumentException(
                'Invalid response object provided'
            );
        }

        $response = $response ?: new Response();


        $dispatcher = call_user_func($this->dispatcher, $this->router, $this->container);

        try {
            $server = new Server($request, $response, $server);
            $server->dispatch(function($request, $response) use ($dispatcher) {
                return $dispatcher->dispatch($request, $response);
            });
            $server->send();
        } catch (HttpRouteNotFoundException $e) {
            $this->log('err', $e->getMessage(), $e);
            $this->notFound ? call_user_func($this->notFound, $e) : null;
        } catch (HttpMethodNotAllowedException $e) {
            $this->log('err', $e->getMessage(), $e);
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

    public function log($type, $message, $extra)
    {
        if (!is_null($this->logger)) {
            $this->logger->$type($message, $extra);
        }
    }
}
