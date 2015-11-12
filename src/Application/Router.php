<?php
namespace Wave\Framework\Application;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wave\Framework\Exceptions\Dispatch\MethodNotAllowedException;
use Wave\Framework\Exceptions\Dispatch\NotFoundException;
use Wave\Framework\Interfaces\Middleware\MiddlewareInterface;

/**
 * Class Router
 * @package Wave\Framework\Application
 */
class Router implements MiddlewareInterface
{
    /**
     * @var RouteCollector
     */
    protected $collector;
    /**
     * @var array
     */
    protected $namedRoutes = [];

    /**
     * @var callable
     */
    protected $errorHandler = null;

    /**
     * @var callable
     */
    protected $notFound = null;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var mixed
     */
    private $cache;

    /**
     * The the 'prefix' and 'suffix' options are, respectively setting global route prefix and suffix.
     * The 'cache' option however must be an array having the 'provider' and optionally 'ttl'. The provider
     * must have the
     *
     * @param array $options Array containing any of the 'prefix', 'suffix', 'cache'
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $option => $value) {
            $this->$option = $value;
        }

        if ($this->prefix !== null) {
            $this->setPrefix($this->prefix);
        }

        $this->collector = new RouteCollector(
            new Std(),
            new GroupCountBased(),
            $this->cache
        );
    }

    /**
     * Sets the prefix to use when adding routes
     *
     * @param $prefix string
     */
    private function setPrefix($prefix)
    {
        $this->prefix = trim($prefix, '/');
    }

    /**
     * Registers a callback to handle errors
     *
     * @param $callback callback
     * @throws \InvalidArgumentException If $callback is not callable
     * @return $this
     */
    public function setErrorHandler($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                'Invalid error handler provided'
            );
        }
        $this->errorHandler = $callback;

        return $this;
    }

    /**
     * Registers a callback to dispatch when a page is not found
     *
     * @param callback $callback
     * @throws \InvalidArgumentException If $callback is not callable
     * @return $this
     */
    public function setNotFound($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                'Invalid 404 callback provided'
            );
        }
        $this->notFound = $callback;

        return $this;
    }

    /**
     * Defines a route which will respond to GET and HEAD requests
     *
     * @param string $pattern The route pattern for the URL
     * @param callable $callback The callback which should be handle the response
     * @param callable[] $middleware List of the middleware for the route
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function get($pattern, $callback, array $middleware = null, $name = null)
    {
        $this->addRoute('get', $pattern, $callback, $middleware, $name);

        return $this;
    }

    /**
     * Proxies the route definition with the backend routing library
     *
     * @param string $method
     * @param string $pattern
     * @param callable $callback
     * @param array $middleware
     * @param null  $name
     */
    protected function addRoute($method, $pattern, $callback, array $middleware = null, $name = null)
    {
        $concatenatedPattern = (!is_null($this->prefix) ? '/' . $this->prefix : '') .
            $pattern;

        $this->collector->addRoute(strtoupper($method), $concatenatedPattern, new Route($callback, $middleware));

        if ($name !== null) {
            $this->namedRoutes[$name] = $concatenatedPattern;
        }
    }

    /**
     * Defines a route which will respond to POST requests
     *
     * @param string $pattern The route pattern for the URL
     * @param callable $callback The callback which should be handle the response
     * @param callable[] $middleware List of the middleware for the route
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function post($pattern, $callback, array $middleware = null, $name = null)
    {
        $this->addRoute('post', $pattern, $callback, $middleware, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to PUT requests
     *
     * @param string $pattern The route pattern for the URL
     * @param callable $callback The callback which should be handle the response
     * @param callable[] $middleware List of the middleware for the route
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function put($pattern, $callback, array $middleware = null, $name = null)
    {
        $this->addRoute('put', $pattern, $callback, $middleware, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to PATCH requests
     *
     * @param string $pattern The route pattern for the URL
     * @param callable $callback The callback which should be handle the response
     * @param callable[] $middleware List of the middleware for the route
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function patch($pattern, $callback, array $middleware = null, $name = null)
    {
        $this->addRoute('patch', $pattern, $callback, $middleware, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to DELETE requests
     *
     * @param string $pattern The route pattern for the URL
     * @param callable $callback The callback which should be handle the response
     * @param callable[] $middleware List of the middleware for the route
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function delete($pattern, $callback, array $middleware = null, $name = null)
    {
        $this->addRoute('delete', $pattern, $callback, $middleware, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to OPTIONS requests
     *
     * @param string $pattern The route pattern for the URL
     * @param callable $callback The callback which should be handle the response
     * @param callable[] $middleware List of the middleware for the route
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function options($pattern, $callback, array $middleware = null, $name = null)
    {
        $this->addRoute('options', $pattern, $callback, $middleware, $name);

        return $this;
    }

    /**
     * Registers a set of routes with the same prefix and/or suffix,
     * isolates the registering from the rest of the code, so everything
     * outside of the callback will not be affected. (avoids dumb variable
     * names containing controller instances, etc)
     *
     * @param callable $callback Usually an anonymous function
     * @param array $options 'prefix' and/or 'suffix' keys only supported
     *
     * @return $this
     */
    public function group($callback, array $options = [])
    {
        $oldPrefix = $this->prefix;
        if (array_key_exists('prefix', $options)) {
            $this->setPrefix($options['prefix']);
        }

        call_user_func($callback, $this);
        $this->setPrefix($oldPrefix);

        return $this;
    }

    /**
     * Returns the route which is registered with $name. Allowing to
     * get a route based on name instead of hard-coding and makes
     * updates to the route pattern easy as it is defined in 1 place
     * and used in many.
     *
     * @param string $name Name of the registered route
     * @param array $args (Optional) Assoc array where parameter names are
     *                    the keys and their values used to make the route
     *
     * @throws \InvalidArgumentException When a route with $name does not
     *                                   exist. Otherwise thrown when the
     *                                   parameters use regex patterns for
     *                                   validation and the provided param
     *                                   does not match it.
     * @throws \LogicException When the number of provided arguments does
     *                         not match the number of parameters in the
     *                         route pattern.
     *
     * @return string
     */
    public function route($name, array $args = [])
    {
        if (!array_key_exists($name, $this->namedRoutes)) {
            throw new \InvalidArgumentException(
                sprintf('Route with name "%s" does not exist', $name)
            );
        }

        if (!is_array($this->namedRoutes[$name])) {
            $pattern = $this->namedRoutes[$name];
            $patterns = [];
            preg_match_all('#\{([A-Z\}:\[\]|+_-])+#i', $pattern, $matches);

            foreach ($matches[0] as $match) {
                if (strpos($match, ':') !== false) {
                    list($param, $validator) = explode(':', substr($match, 1, -1));
                    $patterns[$param] = $validator;

                    $pattern = str_replace($match, '{' . $param .'}', $pattern);
                    continue;
                }
                $patterns[substr($match, 1, -1)] = null;
            }
            $this->namedRoutes[$name] = [$pattern, $patterns];
        }

        $replace = [];

        if (array_diff_key($args, $this->namedRoutes[$name][1]) !== []) {
            throw new \LogicException(
                'Number of arguments does not match the number of bound parameters'
            );
        }

        foreach ($args as $key => $value) {
            if (preg_match('/' . $this->namedRoutes[$name][1][$key] . '/', $value) !== 1) {
                throw new \InvalidArgumentException(sprintf(
                    'The value for parameter "%s" does not match the predefined pattern',
                    $key
                ));
            }
            $replace['{' . $key . '}'] = $value;
        }

        return strtr($this->namedRoutes[$name][0], $replace);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function __invoke($request, $response, $next = null)
    {
        if ($next !== null) {
            $response = $next($request, $response);
        }

        $result = $this->dispatch($request, $response);

        if ($result instanceof ResponseInterface) {
            $response = $result;
        }

        return $response;
    }

    /**
     * Performs the route matching and invokes the handler for the route, if
     * there is an error it throws exception.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     *
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response)
    {

        $method = $request->getMethod();
        $uri = $request->getUri()->getPath();

        $d = new \FastRoute\Dispatcher\GroupCountBased($this->collector->getData());
        $r = $d->dispatch(
            ($method === 'HEAD' ? 'GET' : $method),
            $uri
        );

        switch ($r[0]) {
            case 0:
                throw new NotFoundException('Route not found');
                break;
            case 1:
                return call_user_func($r[1], $request, $response, $r[2]);
                break;
            case 2:
                throw new MethodNotAllowedException('Method not allowed', 0, null, $r[1]);
                break;
        }

        return $response;
    }
}
