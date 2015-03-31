<?php
namespace Wave\Framework\Application;

use Wave\Framework\Adapters\Link\Destination;
use Wave\Framework\Common\Link;
use Wave\Framework\Http\Request;
use Wave\Framework\Http\Server;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;

/**
 * Class Wave
 *
 * @package Wave\Framework\Application
 */
class Wave implements Destination
{

    /**
     * @type \Wave\Framework\Common\Container
     */
    protected $handler;

    /**
     * @var callable the dispatcher ot use with the request
     */
    protected $dispatcher;

    /**
     * @var RouteCollector
     */
    protected $router;


    /**
     * @var $request \Psr\Http\Message\RequestInterface;
     */
    protected $request;


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

    public function setHandler($handler)
    {
        if (!method_exists($handler, 'invoke')) {
            throw new \InvalidArgumentException(
                sprintf('Handler should have method "invoke", not present in class %s', get_class($handler))
            );
        }
        $this->handler = $handler;

        return $this;
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

        if ($callback === null) {
            /**
             * @param $request \Psr\Http\Message\RequestInterface
             * @return mixed
             *
             * @codeCoverageIgnore
             */
            $callback = function ($request) use ($app, $router) {
                try {
                    $dispatcher = call_user_func($app->dispatcher, $router);

                    $result = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
                    if ($result !== null) {
                        return $result;
                    }
                } catch (HttpRouteNotFoundException $e) {
                    $app->handler->invoke('notFound', [$request, $e]);
                } catch (HttpMethodNotAllowedException $e) {
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
