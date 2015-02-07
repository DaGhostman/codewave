<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 06/02/15
 * Time: 00:30
 */

namespace Wave\Framework\Legacy\Router\Bridge;

use Wave\Framework\Legacy\Router\Common\ArgumentsContext;

/**
 * Class Mapper
 * @package Wave\Framework\Legacy\Router\Bridge
 *
 * @deprecated This module is going to be removed in
 *             version 3.5. All users are advised to
 *             upgrade before that.
 */
class Mapper
{
    private $router = null;
    private $map    = null;

    public function __construct($map, $router)
    {
        $this->router = $router;
        $this->map = $map;

        foreach ($map->getRaw()->route as $route) {
            $this->controller(
                $route['via'],
                $this->rebuildUri($route['pattern']),
                $route['controller'],
                $route['method']
            );
        }
    }

    public function rebuildUri($uri)
    {
        preg_match_all('/(:[\w]+)/', $uri, $matches);

        if (isset($matches)) {
            foreach ($matches[0] as $match) {
                $uri = str_replace($match, '{' . substr($match, 1) . '}', $uri);
            }
        }

        return $uri;
    }

    public function controller($method, $uri, $controller, $action)
    {
        if (count(explode(';', $method))) {
            $method = 'ANY';
        }


        $this->router->addRoute($method, $uri, function () use ($controller, $action) {

            $arguments = new ArgumentsContext(null);

            $ref = new \ReflectionClass((string) $controller);
            if ($ref->hasMethod($action)) {
                $controllerAction = $ref->getMethod($action);
                $params = $controllerAction->getParameters();
                foreach ($params as $index => $param) {
                    $arguments->push(substr($param, 1), func_get_arg($index));
                }


                return call_user_func([(string)$controller, (string) $action], $arguments);
            }

            throw new \RuntimeException(sprintf(
                'Method %s not found in controller %s',
                $action,
                $controller
            ));
        });
    }
}
