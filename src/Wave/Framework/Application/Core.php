<?php
namespace Wave\Framework\Application;

use Wave\Framework\Application\Interfaces\ControllerInterface;
use Wave\Framework\Http\Request;

/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 15/08/14
 * Time: 23:15
 */

class Core implements \Serializable, \Iterator, \Countable
{
    protected $controllers;
    private $ioc;
    protected $debug = false;

    protected $config = array(
        'strictPatterns' => false,
        'controllerHandler' => '\Wave\Framework\Application\Controller'
    );

    protected $notFoundPattern = null;

    /**
     * Setups the required properties
     */
    public function __construct($name = 'application', $options = array())
    {
        ob_start();
        $this->ioc = new IoC();

        $app = $this;


        $this->controllers = new \SplQueue();

        $this->config = array_merge($this->config, $options);

        $this->ioc->register('app', function () use ($app) {
            return $app;
        });

    }

    /**
     * Getter for the configurations
     *
     * @param $key string The configuration which needs to be retrieved
     *
     * @return mixed
     */
    public function config($key)
    {
        if (!isset($this->config[$key])) {
            return null;
        }

        return $this->config[$key];
    }

    /**
     * Registers a callback to a specific pattern for
     *  later invocation. Context gets injected in to every
     *  method's constructor.
     *
     * @param $pattern string The URI pattern
     * @param $method string|array The method/s to which the route should respond
     * @param $callback callback The callback
     * @param $conditions array The current context
     * @param $handler string
     *
     * @return Core
     * @throws \InvalidArgumentException
     */
    public function controller(
        $pattern,
        $method,
        $callback,
        array $conditions = array(),
        $handler = null
    ) {

        if (is_null($handler)) {
            $handler = $this->config('controllerHandler');
        }

        $controller = $this->ioc->resolve($handler);


        if (!$controller instanceof ControllerInterface) {
            throw new \InvalidArgumentException("Invalid Controller handler specified");
        }

        $controller->setStrict($this->config('strictPatterns'));

        $controller->setPattern($pattern)
            ->action($callback)
            ->via($method)
            ->conditions($conditions);

        $this->controllers->enqueue($controller);

        return $this;
    }

    /**
     * Get the number of defined Controller
     *
     * @return int The number of defined Controller
     */
    public function numControllers()
    {
        return count($this->controllers);
    }

    /**
     * Invoke all Controller registered to the specified pattern
     *
     * @param $uri string The URI of the request
     * @param $method string The method of the request
     * @param $data array Data to pass to the controller
     *
     * @throws \Exception
     */
    public function invoke($uri, $method, $request, $data = array())
    {
        $script = $request->SCRIPT_NAME;
        $query = '?' . $request->QUERY_STRING;

        $this->controllers->setIteratorMode(
            \SplQueue::IT_MODE_FIFO | \SplQueue::IT_MODE_KEEP
        );

        $matched = false;

        $this->rewind();
        while ($this->valid()) {
            /**
             * @var \Wave\Framework\Application\Interfaces\ControllerInterface
             */
            $controller = $this->current();

            if (substr(urldecode($uri), 0, strlen($script)) == $script) {
                $uri = substr($uri, strlen($script));
                if (substr($uri, 0, (-1 * abs(strlen($query)))) == $query) {
                    $uri = substr($uri, 0, strlen($query));
                }
            }


            if ($controller->match($uri) && $controller->supportsHTTP($method)) {
                $controller->invoke($data);
                $matched = true;
            }

            $this->next();
        }

        if (!$matched && $this->notFoundPattern) {
            $this->redirect($this->notFoundPattern, $data);
        }
    }

    /**
     * Clears all registered controllers
     *
     * @return $this
     */
    public function clearControllers()
    {
        $this->controllers = new \SplQueue();

        return $this;
    }

    /**
     * Defines a default 404 handle, triggered when no route have matched the request
     *
     * @param $pattern string defines a pattern for the 404 page
     * @param $callback callable Callback to invoke if no route is matched
     */
    public function notFound($pattern, $callback)
    {
        $this->notFoundPattern = $pattern;
        $this->controller($pattern, array('GET', 'POST', 'PUT', 'DELETE', 'CLI'), $callback);
    }


    /**
     * Performs internal redirection and once the redirection has finished, it
     * terminates the script execution using 'exit(0);' so no further execution
     * will happen.
     *
     * @param string $location The uri to redirect to
     * @param array $data Same as 3rd argument of Core::run
     */
    public function redirect($location, $data = array())
    {
        $req = array_merge($_SERVER, array('REQUEST_URI' => $location, 'REQUEST_METHOD' => 'GET'));

        $request = new Request($req);

        $this->run($request, null, $data);
        $this->clearControllers();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->controllers->current();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->controllers->next();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->controllers->key();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->controllers->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->controllers->rewind();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     *
     * @codeCoverageIgnore
     */
    public function serialize()
    {
        $container = new \SplQueue();

        $this->rewind();
        while ($this->valid()) {
            $controller = $this->current();

            if ($controller->isSerializeable()) {
                $container->enqueue($controller);
            }

            $this->next();
        }

        return serialize($container);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function unserialize($serialized)
    {
        $this->ioc = new IoC();
        $this->controllers = unserialize($serialized);
    }

    /**
     * Turns on debugging
     *
     * @return $this
     */
    public function debug()
    {
        $this->debug = true;

        return $this;
    }

    /**
     * @param \Wave\Framework\Http\Request $request
     * @param \Wave\Framework\Http\Response $response
     * @param array $data Custom data to pass to the controller
     *
     * @throws \Exception
     */
    public function run($request, $response = null, $data = array())
    {
        try {
            $this->invoke($request->uri(), $request->method(), $request, $data);
        } catch (\Exception $e) {
            if ($this->debug) {
                ob_clean();

                echo sprintf(
                    "Error occurred: \"%s\"(%s) in %s:%s \n\r\n\r%s \n\r",
                    $e->getMessage(),
                    $e->getCode(),
                    $e->getFile(),
                    $e->getLine(),
                    print_r($e->getTraceAsString(), true)
                );
            }

            /**
             * @codeCoverageIgnoreStart
             */
            if ($response) {
                $response->internalError();
                $response->header('Content-Type: text/plain');
                $response->send();
            }
            /**
             * @codeCoverageIgnoreEnd
             */

            echo ob_get_clean();
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->controllers);
    }
}
