<?php
namespace Wave\Framework\Application;

use Wave\Framework\Adapters\Link\Destination;
use Wave\Framework\Http\Request;
use Wave\Framework\Http\Server;

/**
 * Class Wave
 *
 * @package Wave\Framework\Application
 *
 * This is the application class, which at the moment serves mostly as
 * a gluing object between all the interaction
 */
class Wave implements Destination
{
    /**
     * @var callable the dispatcher ot use with the request
     */
    protected $dispatcher;


    /**
     * @var $request Request
     */
    protected $request;

    /**
     * @var $request \Wave\Framework\Http\Response;
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
        self::setInstance($this);
    }

    /**
     * Sets the request from the link after run() is called
     *
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
     * @return null|Request
     */
    public static function getRequest()
    {
        return self::$instance->request;
    }



    /**
     * Sets the response object once the server is instantiated
     *
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
     * @return \Wave\Framework\Http\Response
     */
    public static function getResponse()
    {
        return self::$instance->response;
    }

    /**
     * Allows using this object to retrieve the request and response
     * objects and keep them available in the global scope
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
     * @param $serverVars array Usually should default to $_SERVER
     * @param $callback callable
     */
    public function run($serverVars = null, callable $callback = null)
    {
        if ($serverVars === null) {
            $serverVars = filter_input_array(INPUT_SERVER);
        }

        $server = new Server($serverVars);
        $this->request = $server->getRequest();
        $this->response = $server->getResponse();

        if ($callback === null) {
            throw new \InvalidArgumentException('Invalid callback provided');
        }

        $server->listen($callback);
    }
}
