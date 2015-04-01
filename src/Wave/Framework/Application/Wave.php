<?php
namespace Wave\Framework\Application;

use Wave\Framework\Adapters\Link\Destination;
use Wave\Framework\Common\Link;
use Wave\Framework\Http\Request;
use Wave\Framework\Http\Response;
use Wave\Framework\Http\Server;

/**
 * Class Wave
 *
 * @package Wave\Framework\Application
 */
class Wave implements Destination
{
    /**
     * @var callable the dispatcher ot use with the request
     */
    protected $dispatcher;

    /**
     * @var object
     */
    protected $router;


    /**
     * @var $request \Psr\Http\Message\RequestInterface;
     */
    protected $request;

    /**
     * @var $request \Psr\Http\Message\ResponseInterface;
     */
    protected $response;


    protected static $instance;

    /**
     * Takes a callback, which is invoked to create the dispatcher.
     * The only argument passed to the callback is the Router object
     *
     * @param $dispatcher callable
     * @throws \InvalidArgumentException
     */
    public function __construct(callable $dispatcher)
    {
        $this->link = new Link($this);

        $this->dispatcher = $dispatcher;
        self::setInstance($this);
    }

    /**
     * @param $request Request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }



    /**
     * @param $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Allows the dispatcher to be changed at runtime
     *
     * @param $dispatcher callable
     * @return $this
     */
    public function setDispatcher(callable $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * This method should be used right after initialization of the
     * application class, so it could work as proxy for the route generation.
     *
     * @param $router
     * @return $this
     */
    public function setRouter($router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Helps with the building of a singleton object
     *
     * @param $instance Wave
     */
    private static function setInstance($instance)
    {
        self::$instance = $instance;
    }

    /**
     * Proxies all method calls to the router instance
     *
     * @param string $name
     * @param array $args
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __call($name, $args)
    {
        if (is_object($this->router)) {
            return call_user_func_array([
                $this->router,
                $name
            ], $args);
        }

        throw new \RuntimeException(
            sprintf('Expected router of type object, "%s" received', gettype($this->router))
        );
    }

    /**
     * Passes the request object to use for the request, recommended to use the
     * Factory\Request::build method's result directly. It invokes the \Phly\Http\ServerRequestFactory,
     * which already builds the object as per PSR-7
     *
     * @param $request \Psr\Http\Message\RequestInterface
     * @param $callback callable
     */
    public function run($request, callable $callback = null)
    {
        $app = $this;
        $router = $this->router;

        /**
         * @type Request
         */
        $request = new Request($request);
        $server = new Server($request);

        (new Link($this))->push($server->request, 'setRequest');
        (new Link($this))->push($server->response, 'setResponse');

        if ($callback === null) {
            /**
             * @param $request \Psr\Http\Message\RequestInterface
             * @return mixed
             *
             * @codeCoverageIgnore
             */
            $callback = function ($request) use ($app, $router) {
                $container = \Wave\Framework\Common\Container::getInstance();
                $di = new \Wave\Framework\Common\DependencyResolver($container);


                try {
                    $dispatcher = call_user_func(
                        $app->dispatcher,
                        $router,
                        new \Wave\Framework\External\Phroute\RouteResolver($di)
                    );

                    $result = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
                    if ($result !== null) {
                        return $result;
                    }


                } catch (\Phroute\Phroute\Exception\HttpRouteNotFoundException $e) {
                    $app->handler->invoke('notFound', [$request, $e]);
                } catch (\Phroute\Phroute\Exception\HttpMethodNotAllowedException $e) {
                    $app->handler->invoke('notAllowed', [$request, $e]);
                } catch (\Exception $e) {
                    $app->handler->invoke('serverError', [$request, $e]);
                }
            };
        }

        $server->listen($callback)
            ->send();
    }
}
