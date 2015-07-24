<?php
namespace Wave\Framework\Application;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteParser\Std;
use Wave\Framework\Router\ExtendedRouteCollector as RouteCollector;
use Wave\Framework\Exceptions\HttpNotAllowedException;
use Wave\Framework\Exceptions\HttpNotFoundException;
use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;

class Router
{
    /**
     * @var RouteCollector
     */
    protected $collector;
    /**
     * @var array
     */
    protected $namedRoutes = [];
    private $prefix;
    private $suffix;
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
     * Defines a route which will respond to GET and HEAD requests
     *
     * @param string $pattern The route pattern for the URL
     * @param array $callback The callback which should be handle the response
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function get($pattern, array $callback, $name = null)
    {
        $this->addRoute('get', $pattern, $callback, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to POST requests
     *
     * @param string $pattern The route pattern for the URL
     * @param array $callback The callback which should be handle the response
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function post($pattern, array $callback, $name = null)
    {
        $this->addRoute('post', $pattern, $callback, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to PUT requests
     *
     * @param string $pattern The route pattern for the URL
     * @param array $callback The callback which should be handle the response
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function put($pattern, array $callback, $name = null)
    {
        $this->addRoute('put', $pattern, $callback, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to PATCH requests
     *
     * @param string $pattern The route pattern for the URL
     * @param array $callback The callback which should be handle the response
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function patch($pattern, array $callback, $name = null)
    {
        $this->addRoute('patch', $pattern, $callback, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to DELETE requests
     *
     * @param string $pattern The route pattern for the URL
     * @param array $callback The callback which should be handle the response
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function delete($pattern, array $callback, $name = null)
    {
        $this->addRoute('delete', $pattern, $callback, $name);

        return $this;
    }

    /**
     * Defines a route which will respond to OPTIONS requests
     *
     * @param string $pattern The route pattern for the URL
     * @param array $callback The callback which should be handle the response
     * @param string $name (Optional) Route name. Used for reverse routing.
     *
     * @return $this
     */
    public function options($pattern, array $callback, $name = null)
    {
        $this->addRoute('options', $pattern, $callback, $name);

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

        $oldSuffix = $this->suffix;
        if (array_key_exists('suffix', $options)) {
            $this->setSuffix($options['suffix']);
        }

        call_user_func($callback, $this);
        $this->setPrefix($oldPrefix);
        $this->setSuffix($oldSuffix);

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
     * Performs the route matching and invokes the handler for the route, if
     * there is an error it throws exception.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @throws HttpNotAllowedException
     * @throws HttpNotFoundException
     *
     * @return void
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response)
    {
        $d = new \FastRoute\Dispatcher\GroupCountBased($this->collector->getData());
        $r = $d->dispatch(
            ($request->getMethod() === 'HEAD' ? 'GET' : $request->getMethod()),
            $request->getUrl()
                ->getPath()
        );

        switch ($r[0]) {
            case 0:
                throw new HttpNotFoundException('Route not found');
                break;
            case 1:
                call_user_func($r[1], $request, $r[2], $response);
                break;
            case 2:
                throw new HttpNotAllowedException('Method not allowed', 0, null, $r[1]);
                break;
        }
    }

    /**
     * Proxies the route definition with the backend routing library
     *
     * @param string $method
     * @param string $pattern
     * @param array $callback
     * @param null  $name
     */
    protected function addRoute($method, $pattern, array $callback, $name = null)
    {
        $concatenatedPattern = (!is_null($this->prefix) ? '/' . $this->prefix : '') .
            $pattern .
            ($this->suffix ? $this->suffix : '');

        $this->collector->addRoute(strtoupper($method), $concatenatedPattern, $callback);

        if ($name !== null) {
            $this->namedRoutes[$name] = $concatenatedPattern;
        }
    }

    private function setPrefix($prefix)
    {
        $this->prefix = trim($prefix, '/');
    }

    private function setSuffix($suffix)
    {
        $this->prefix = trim($suffix, '/');
    }
}
