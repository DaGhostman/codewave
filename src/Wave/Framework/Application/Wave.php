<?php
namespace Wave\Framework\Application;

use Wave\Framework\Adapters\Link\Destination;
use Wave\Framework\Common\Link;
use Wave\Framework\Http\Request;
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


    /**
     * @type Wave
     */
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
        if (!is_callable($dispatcher)) {
            throw new \InvalidArgumentException(
                'The dispatcher factory provided is invalid'
            );
        }

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

    /**
     * Returns the current value of the request for the application
     *
     * @return null|\Psr\Http\Message\RequestInterface
     */
    public static function getRequest()
    {
        return self::$instance->request;
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

    /**
     * Returns the response created for the function.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function getResponse()
    {
        return self::$instance->response;
    }

    /**
     * Allows the dispatcher to be changed at runtime.
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
             * @throws \Exception
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
                    echo $e->getMessage();
                } catch (\Phroute\Phroute\Exception\HttpMethodNotAllowedException $e) {
                    echo $e->getMessage();
                } catch (\Exception $e) {
                    throw new \ErrorException($e->getMessage(), null, $e);
                }
            };
        }

        $server->listen($callback)
            ->send();
    }
}
