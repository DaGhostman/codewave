<?php
/**
 *
 * This package is originally part of the Slim Framework.
 * The details bellow are of the original author and he is
 * not related in anyway with this project. Report problems
 * on this project's GitHub Page and not on the original project.
 * The original author is freed from responsibility and/or
 * obligations to this component or any future version of it.
 * 
 * All author details are left as appreciation to his hard work
 * and time spent on the project.
 *
 * @author      Josh Lockhart <info@slimframework.com>
 * @copyright   2011 Josh Lockhart
 * @link        http://www.slimframework.com
 * @license     http://www.slimframework.com/license
 * @version     2.4.2
 * @package     Slim
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Wave\Application;


/**
 * Router
 *
 * This class organizes, iterates, and dispatches \Wave\Route objects.
 *
 * @package Wave\Application
 * @author Josh Lockhart
 * @since 1.0.0
 */
class Controller
{

    /**
     *
     * @var Route The current route (most recently dispatched)
     */
    protected $currentRoute;

    /**
     *
     * @var array Lookup hash of all route objects
     */
    protected $routes;

    /**
     *
     * @var array Lookup hash of named route objects,
     *              keyed by route name (lazy-loaded)
     */
    protected $namedRoutes;

    /**
     *
     * @var array Array of route objects that match the request URI (lazy-loaded)
     */
    protected $matchedRoutes;

    /**
     *
     * @var array Array containing all route groups
     */
    protected $routeGroups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->routes = array();
        $this->routeGroups = array();
    }

    /**
     * Get Current Route object or the first matched one,
     *            if matching has been performed
     * 
     * @return Route null
     */
    public function getCurrentRoute()
    {
        if ($this->currentRoute !== null) {
            return $this->currentRoute;
        }
        
        if (is_array($this->matchedRoutes) && count($this->matchedRoutes) > 0) {
            return $this->matchedRoutes[0];
        }
        
        return null;
    }

    /**
     * Return route objects that match the given HTTP method and URI
     * 
     * @param string $httpMethod
     *            The HTTP method to match against
     * @param string $resourceUri
     *            The resource URI to match against
     * @param bool $reload
     *            Should matching routes be re-parsed?
     * @return array[\Wave\Route]
     */
    public function getMatchedRoutes($httpMethod, $resourceUri, $reload = false)
    {
        if ($reload || is_null($this->matchedRoutes)) {
            $this->matchedRoutes = array();
            foreach ($this->routes as $route) {
                if (! $route->supportsHttpMethod($httpMethod) &&
                    ! $route->supportsHttpMethod("ANY")) {
                    continue;
                }
                
                if ($route->matches($resourceUri)) {
                    $this->matchedRoutes[] = $route;
                }
            }
        }
        return $this->matchedRoutes;
    }

    /**
     * Add a route object to the router
     * 
     * @param Route $route
     *            The Wave Route
     */
    public function map(Route $route)
    {
        $route->setPattern($route->getPattern());
        $this->routes[] = $route;
    }

    /**
     * Get URL for named route
     * 
     * @param string $name
     *            The name of the route
     * @param array $params
     *            Associative array of URL parameter names and replacement values
     * @throws \RuntimeException If named route not found
     * @return string The URL for the given route populated with provided replacement values
     */
    public function urlFor($name, $params = array())
    {
        if (! $this->hasNamedRoute($name)) {
            throw new \RuntimeException('Named route not found for name: ' . $name);
        }
        $search = array();
        foreach ($params as $key => $value) {
            $search[] = '#:' . preg_quote($key, '#') . '\+?(?!\w)#';
        }
        $pattern = preg_replace(
            $search,
            $params,
            $this->getNamedRoute($name)->getPattern()
        );
        
        // Remove remnants of unpopulated, trailing optional pattern segments, escaped special characters
        return preg_replace('#\(/?:.+\)|\(|\)|\\\\#', '', $pattern);
    }

    /**
     * Add named route
     * 
     * @param string $name
     *            The route name
     * @param Route $route
     *            The route object
     *
*@throws \RuntimeException If a named route already exists with the same name
     */
    public function addNamedRoute($name, Route $route)
    {
        if ($this->hasNamedRoute($name)) {
            throw new \RuntimeException('Named route already exists with name: ' . $name);
        }
        $this->namedRoutes[(string) $name] = $route;
    }

    /**
     * Checks if a named route with <em>$name</em> exists
     * 
     * @param string $name
     *            The route name
     * @return bool
     */
    public function hasNamedRoute($name)
    {
        $this->getNamedRoutes();
        
        return isset($this->namedRoutes[(string) $name]);
    }

    /**
     * Returns route with specified <em>$name</em> if exists,
     *          null otherwise.
     * 
     * @param string $name            
     *
     * @return mixed Route or null
     */
    public function getNamedRoute($name)
    {
        $this->getNamedRoutes();
        if ($this->hasNamedRoute($name)) {
            return $this->namedRoutes[(string) $name];
        } else {
            return null;
        }
    }

    /**
     * Gets all named routes
     * 
     * @return \ArrayIterator
     */
    public function getNamedRoutes()
    {
        if (is_null($this->namedRoutes)) {
            $this->namedRoutes = array();
            foreach ($this->routes as $route) {
                if ($route->getName() !== null) {
                    $this->addNamedRoute($route->getName(), $route);
                }
            }
        }
        
        return new \ArrayIterator($this->namedRoutes);
    }
}
