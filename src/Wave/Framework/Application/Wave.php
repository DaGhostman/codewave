<?php
namespace Wave\Framework\Application;

use Psr\Http\Message\RequestInterface;
use Wave\Framework\Router\Dispatcher;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;
use Wave\Framework\Router\Resolver;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Wave
 *
 * @package Wave\Framework\Application
 */
class Wave
{

    /**
     * @var callable Function to use for 404
     */
    protected $notFound = null;

    /**
     * @var callable Function to use for 405
     */
    protected $notAllowed = null;

    /**
     * @var array|\ArrayAccess
     */
    protected $container = null;

    /**
     * @var callable the dispatcher ot use with the request
     */
    protected $dispatcher = null;

    /**
     * @var RouteCollector
     */
    protected $router = null;

    /**
     * Creates the main application instance with the
     * DI container as 1st argument, this container is
     * later passed on to the Router\Resolver for route
     * resolution.
     *
     * @param $container array|\ArrayAccess
     *            Instance of Pimple
     * @throws \InvalidArgumentException
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->router = new RouteCollector();

        $router = $this->router;
        if (! is_null($container)) {
            /**
             * @return mixed
             *
             * @codeCoverageIgnore
             */
            $container['router'] = function () use ($router) {
                return $router;
            };
        }

        /**
         * @param $router RouteCollector
         * @param $container
         * @return Dispatcher
         */
        $this->dispatcher = function ($router, $container) {
            return new Dispatcher(
                $router->getData(),
                new Resolver($container)
            );
        };
    }

    /**
     * Returns the RouteCollector in use
     *
     * @return RouteCollector
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Proxies all method calls to the PHRoute instance
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array([
            $this->router,
            $name
        ], $args);
    }

    /**
     * Starts the application routing.
     * Second argument is passed directly to the dispatcher. See
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $server Array equiv to $_SERVER variable
     */
    public function run(RequestInterface $request, ResponseInterface $response, $server = null)
    {

        if (!is_null($response) && !$response instanceof ResponseInterface) {
            throw new \InvalidArgumentException(
                'Invalid response object provided'
            );
        }

        $dispatcher = call_user_func($this->dispatcher, $this->router, $this->container);

        try {
            $server = new Server($request, $response, $server);
            $server->dispatch(function ($request, $response) use ($dispatcher) {
                if ($dispatcher instanceof Dispatcher) {
                    return $dispatcher->dispatch($request, $response);
                }

                return 0;
            });
            $server->send();
        } catch (HttpRouteNotFoundException $e) {
            $this->notFound ? call_user_func($this->notFound, $e) : null;
        } catch (HttpMethodNotAllowedException $e) {
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
}
