<?php
namespace Wave\Application;

use Wave\Application;
use Wave\Http;
use Wave\Pattern\Observer\Subject;
use Wave\Storage\Registry;

/**
 * Loader
 * 
 * @package Wave
 * @author Dimitar Dimitrov
 * @since 1.0.0
 *
 * @method Route get(string $pattern, callable $callback) Defines a GET route
 * @method Route post(string $pattern, callable $callback) Defines a POST route
 * @method Route put(string $pattern, callable $callback) Defines a PUT route
 * @method Route delete(string $pattern, callable $callback) Defines a DELETE route
 * @method Route head(string $pattern, callable $callback) Defines a HEAD route
 * @method Route trace(string $pattern, callable $callback) Defines a TRACE route
 * @method Route connect(string $pattern, callable $callback) Defines a CONNECT route
 * @method Route options(string $pattern, callable $callback) Defines a OPTIONS route
 * @method Registry environment() Getter for environment
 * @method Registry config() Getter for configuration passed to __construct
 * @method Controller controller() Returns the Controller object
 * @method Http\Factory http(string $key) Getter for Http objects. Valid values 'request', 'response'
 */
class Loader extends Subject
{

    /**
     * Container for the Environment object
     * 
     * @var Registry
     */
    protected $environment = null;

    /**
     * Container for the Http\Factory object
     * 
     * @var \Wave\Http\Factory
     */
    protected $http = null;

    /**
     * Container for the Controller
     * 
     * @var \Wave\Application\Controller
     */
    protected $controller = null;

    /**
     * The placeholder for the default configuration
     * 
     * @var array
     */
    protected $defaultConfig = null;

    /**
     * The configuration currently in use
     * 
     * @var array
     */
    protected $config = null;

    /**
     * View engine
     */
    protected $view = null;

    /**
     * Constructs the Loader class and injects an array with dependencies
     * for the application.
     * After the instantiation only the containers
     * for Environment and Controller are available.
     *
     * @param mixed $config
     *            array|Zend_Config instance or array with configurations
     *
     * @param mixed $routes array|Zend_Config instance or array with route definitions
     */
    public function __construct($config = array(), $routes = array())
    {

        $this->config = new Registry(array(
            "mutable" => false,
            "override" => false,
            "data" => $config
        ));

        $env = array(
            'request.protocol' => (isset($_SERVER['SERVER_PROTOCOL']) ?
                $_SERVER['SERVER_PROTOCOL'] : 'HTTP\1.1'),
            'request.port' => (isset($_SERVER['SERVER_PORT']) ?
                    $_SERVER['SERVER_PORT'] : 80),
            'request.uri' => (isset($_SERVER['REQUEST_URI']) ?
                    $_SERVER['REQUEST_URI'] : '/'),
            'request.method' => (isset($_SERVER['REQUEST_METHOD']) ?
                    $_SERVER['REQUEST_METHOD'] : 'GET')
        );


        $this->controller = new Controller();
        $this->environement = new Registry(array(
            'mutable' => true,
            'override' => false,
            'data' => array_merge($env, (isset($config['environment']) ?
                $config['environment'] : array()))
        ));


        foreach ($routes as $route) {

            $r = $this->map($route['pattern'], $route['callback']);
            call_user_func_array(array($r, 'via'), $route['method']);

            if (isset($route['name']) && $route['name'] != null) {
                $r->name($route['name']);
            }
            if (isset($route['conditions']) && null != $route['conditions']) {
                $r->conditions($route['conditions']);
            }
        }


        /*
         * Build the HTTP handlers
         */
        $this->state('httpBefore')->notify($this->environement);

        $this->http = new Http\Factory(
            new Http\Request($this->environement),
            new Http\Response()
        );

        $this->state('httpAfter')->notify($this->environement);
    }

    public function registerRoutes($routes)
    {

    }

    /**
     * Setter for view objects, which should handle template generation
     *
     * @param $viewer mixed View handling object
     */
    public function setView($viewer)
    {
        $this->view = $viewer;
    }

    /**
     * @param $key string Variable to get
     *
     * @return mixed the variable value
     */
    public function __get($key)
    {
        if (!isset($this->$key)) {
            return null;
        }

        return $this->$key;
    }

    /**
     * Responsible for route registering and as a magic getter of
     *          object properties.
     *
     *
     *
     * @param string $name The variable name to access
     * @param array $args the key of the value to retrieve,
     *                    currently only useful for
     *                    when getting config options.
     *
     * @return mixed|null
     */
    public function __call($name, $args = array())
    {
        $result = null;

        $methods = array(
            'get', 'head', 'post', 'put', 'delete', 'trace', 'connect', 'options'
        );

        if (in_array(strtolower($name), $methods)) {
            return $this->mapRoute($args)->via(strtoupper($name));
        } else {
            if (isset($this->$name)) {
                if (!empty($args)) {
                    if ($this->$name instanceof Registry) {
                        $result = $this->$name->get($args[0]);
                    } else {
                        if (isset($this->{$name}[$args[0]])) {
                            $result = $this->{$name}[$args[0]];
                        } else {
                            $result = null;
                        }
                    }
                } else {
                    $result = $this->$name;
                }
            } else {
                $result = null;
            }
        }

        return $result;
    }

    /**
     * This method is more like a placeholder for functionality
     * which is to be added in later versions.
     * This method instantiates the Http\Factory and calls all
     * observers using 'http_before' and 'http_after'
     * methods, respectively before and after the factory is instantiated.
     *
     * @deprecated This method is obsolete since, v1.1.0 and will be removed in 1.5
     * @return \Wave\Application\Loader Current instance for chaining
     */
    public function bootstrap()
    {
        return $this;
    }

    /*****************************
     * Slim specific code follows *
     *****************************/


    /**
     * Maps first argument as pattern for the callable (second argument)
     *
     * @return Route
     */
    public function map()
    {
        $args = func_get_args();

        return $this->mapRoute($args);
    }

    /**
     * Creates the route
     *
     * @param array $args
     *            Arguments for the route
     * @return \Wave\Application\Route
     */
    protected function mapRoute($args)
    {
        $pattern = array_shift($args);
        $callable = array_pop($args);
        $route = new Route($pattern, $callable, false);
        $this->controller->map($route);
        
        return $route;
    }

    /*********************************
     * Modified Slim Framework Code  *
     *********************************/
    /**
     * This method starts the actual user-land part of the code,
     * it iterates over the registered routes, if none are found it
     * directly return a 404 to the user, except cases where the headers
     * are already sent.
     *
     * Notifies observers using 'map_before' and 'map_after', respectively in the mapping phase.
     * Notifies observers using 'dispatch_before' and 'dispatch_after', respectively in the routing phase.
     * Notifies observers using 'application_after' once the mapping has finished.
     *
     * @throws \Exception
     */
    public function run()
    {
        $this->state('mapBefore')
            ->notify($this->environement);
        
        try {
            $dispatched = false;
            $matchedRoutes = $this->controller
                ->getMatchedRoutes(
                    $this->environement['request.method'],
                    $this->environement['request.uri']
                );
            
            foreach ($matchedRoutes as $route) {
                try {
                    $this->state('dispatchBefore')->notify();
                    
                    $dispatched = $route->dispatch();
                    
                    $this->state('dispatchAfter')->notify();
                    
                    if ($dispatched) {
                        break;
                    }
                } catch (Application\State\Pass $e) {
                    continue;
                } catch (Application\State\Halt $e) {
                    break;
                }
            }
            if (!$dispatched) {
                $this->http->response()
                    ->notFound()
                    ->send();
            }
        } catch (\Exception $e) {
            if ($this->config('debug')) {
                throw $e;
            }
        }
        
        $this->state('mapAfter')->notify($this->environement);
        
        $this->state('applicationAfter')->notify($this->environement);
    }
}
