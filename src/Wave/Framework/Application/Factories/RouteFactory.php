<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 21/08/14
 * Time: 00:11
 */

namespace Wave\Framework\Application\Factories;


/**
 * Class RouteFactory
 * Registers routes from a XML map file
 *
 * @package Wave\Framework\Application\Factories
 */
class RouteFactory
{

    /**
     * Register all routes with the application
     *
     * @static
     * @access public
     *
     * @param $map string Route map file or XML string
     * @param $app \Wave\Framework\Application\Core|\Wave\Framework\Application\Module
     *
     * @throws \LogicException
     */
    public static function build($map, &$app)
    {

        $xml = new \SimpleXMLElement($map, null, is_file($map));


        foreach ($xml->route as $index => $route) {

            self::validateRoute($route, $index);

            $conditions = array();
            if (isset($route->conditions)) {
                foreach ($route->conditions->condition as $condition) {
                    if ($condition['rule']) {
                        $conditions[(string) $condition['name']] = (string) $condition['rule'];
                    }
                }
            }

            $handler = '\Wave\Framework\Application\Controller';
            if (isset($route['handler'])) {
                if (!class_exists($route['handler'], true)) {
                    throw new \LogicException(
                        sprintf("Invalid Controller Handler specified for controller #%d", $index)
                    );
                }
            }

            $controller = (string) $route['controller'];

            $app->controller(
                (string) $route['pattern'],
                explode(';', $route['via']),
                array(new $controller, (string) $route['method']),
                $conditions,
                $handler
            );
        }
    }

    public static function validateRoute($route, $index)
    {
        if (!(bool) $route['controller']) {
            throw new \RuntimeException(
                sprintf("Controller not specified for route #%d", (string) $index)
            );
        }

        if (!(bool) $route['method']) {
            throw new \RuntimeException(
                sprintf("Method not specified for route #%d", (string) $index)
            );
        }

        if (!(bool) $route['pattern']) {
            throw new \RuntimeException(
                sprintf("Pattern not specified for route #%d", (string) $index)
            );
        }

        return true;
    }
}
