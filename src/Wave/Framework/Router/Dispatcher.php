<?php
namespace Wave\Framework\Router;

use \Phroute\Phroute\Dispatcher as D;
use \Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use \Phroute\Phroute\Exception\HttpRouteNotFoundException;
use \Phroute\Phroute\RouteDataInterface;
use \Phroute\Phroute\HandlerResolverInterface;
use \Phroute\Phroute\HandlerResolver;
use \Phroute\Phroute\Route;

class Dispatcher extends D
{

    private $staticRouteMap;

    private $variableRouteData;

    /**
     * @var \Phroute\Phroute\HandlerResolver
     */
    private $handlerResolver;

    public $matchedRoute;
    protected $filters;

    /**
     * Create a new route dispatcher.
     *
     * @param RouteDataInterface $data
     * @param HandlerResolverInterface $resolver
     */
    public function __construct(RouteDataInterface $data, HandlerResolverInterface $resolver = null)
    {
        $this->staticRouteMap = $data->getStaticRoutes();
        $this->variableRouteData = $data->getVariableRoutes();
        $this->filters = $data->getFilters();

        $this->handlerResolver = $resolver;

        if ($resolver === null) {
            $this->handlerResolver = new HandlerResolver();
        }
    }

    /**
     * Dispatch a route for the given HTTP Method / URI.
     *
     * @param \Wave\Framework\Http\Server\Request $request
     * @param \Wave\Framework\Http\Server\Response $response
     * @return mixed|null
     */
    public function dispatch($request, $response)
    {
        list ($handler, $filters, $vars) = $this->dispatchRoute(
            $request->getMethod(),
            trim($request->getUri()->getPath(), '/')
        );

        list ($beforeFilter, $afterFilter) = $this->parseFilters($filters);

        if (($output = $this->dispatchFilters($beforeFilter)) !== null) {
            $response->getBody()
                ->write($output);
            return $response;
        }

        $resolvedHandler = $this->handlerResolver->resolve($handler);

        $params = new \ArrayObject($vars, \ArrayObject::ARRAY_AS_PROPS);

        if (!is_array($resolvedHandler) && !$resolvedHandler instanceof \Closure) {
            throw new \LogicException(sprintf(
                'Route handler expected to be either array or string, %s given',
                gettype($resolvedHandler)
            ));
        }

        if (is_array($resolvedHandler)) {
            $response = call_user_func_array($resolvedHandler, $vars);
        }

        if ($resolvedHandler instanceof \Closure) {
            $response = call_user_func_array($resolvedHandler, [
                $params,
                $request,
                $response
            ]);
        }

        return $this->dispatchFilters($afterFilter, $response);
    }

    /**
     * Dispatch a route filter.
     *
     * @param
     *            $filters
     * @param null $response
     * @return mixed|null
     */
    protected function dispatchFilters($filters, $response = null)
    {
        while ($filter = array_shift($filters)) {
            $handler = $this->handlerResolver->resolve($filter);

            if (($filteredResponse = call_user_func($handler, $response)) !== null) {
                return $filteredResponse;
            }
        }

        return $response;
    }

    /**
     * Normalise the array filters attached to the route and merge with any global filters.
     *
     * @param
     *            $filters
     * @return array
     */
    protected function parseFilters($filters)
    {
        $beforeFilter = array();
        $afterFilter = array();

        if (isset($filters[Route::BEFORE])) {
            $beforeFilter = array_intersect_key($this->filters, array_flip((array) $filters[Route::BEFORE]));
        }

        if (isset($filters[Route::AFTER])) {
            $afterFilter = array_intersect_key($this->filters, array_flip((array) $filters[Route::AFTER]));
        }

        return array(
            $beforeFilter,
            $afterFilter
        );
    }

    /**
     * Perform the route dispatching.
     * Check static routes first followed by variable routes.
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @throws HttpRouteNotFoundException
     * @return mixed
     */
    protected function dispatchRoute($httpMethod, $uri)
    {
        if (isset($this->staticRouteMap[$uri])) {
            return $this->dispatchStaticRoute($httpMethod, $uri);
        }

        return $this->dispatchVariableRoute($httpMethod, $uri);
    }

    /**
     * Handle the dispatching of static routes.
     *
     * @param
     *            $httpMethod
     * @param
     *            $uri
     * @return mixed
     * @throws \Phroute\Phroute\Exception\HttpMethodNotAllowedException
     */
    protected function dispatchStaticRoute($httpMethod, $uri)
    {
        $routes = $this->staticRouteMap[$uri];

        if (! isset($routes[$httpMethod])) {
            $httpMethod = $this->checkFallbacks($routes, $httpMethod);
        }

        return $routes[$httpMethod];
    }

    /**
     * Check fallback routes: HEAD for GET requests followed by the ANY attachment.
     *
     * @param mixed $routes
     * @param mixed $httpMethod
     * @throws \Phroute\Phroute\Exception\HttpMethodNotAllowedException
     */
    protected function checkFallbacks($routes, $httpMethod)
    {
        $additional = array(
            Route::ANY
        );

        if ($httpMethod === Route::HEAD) {
            $additional[] = Route::GET;
        }

        foreach ($additional as $method) {
            if (isset($routes[$method])) {
                return $method;
            }
        }

        $this->matchedRoute = $routes;

        throw new HttpMethodNotAllowedException('Allow: ' . implode(', ', array_keys($routes)));
    }

    /**
     * Handle the dispatching of variable routes.
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @throws \Phroute\Phroute\Exception\HttpMethodNotAllowedException
     * @throws \Phroute\Phroute\Exception\HttpRouteNotFoundException
     * @throws
     */
    protected function dispatchVariableRoute($httpMethod, $uri)
    {
        foreach ($this->variableRouteData as $data) {
            if (! preg_match($data['regex'], $uri, $matches)) {
                continue;
            }

            $count = count($matches);

            while (! isset($data['routeMap'][$count ++])) {
            }

            $routes = $data['routeMap'][$count - 1];

            if (! isset($routes[$httpMethod])) {
                $httpMethod = $this->checkFallbacks($routes, $httpMethod);
            }

            foreach (array_values($routes[$httpMethod][2]) as $i => $varName) {
                if (! isset($matches[$i + 1]) || $matches[$i + 1] === '') {
                    unset($routes[$httpMethod][2][$varName]);
                    continue;
                }

                $routes[$httpMethod][2][$varName] = $matches[$i + 1];
            }

            return $routes[$httpMethod];
        }

        throw new HttpRouteNotFoundException('Route ' . $uri . ' does not exist');
    }
}
