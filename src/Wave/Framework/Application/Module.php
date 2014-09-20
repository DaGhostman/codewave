<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 13/09/14
 * Time: 23:55
 */

namespace Wave\Framework\Application;

use Wave\Framework\Application\Factories\RouteFactory;

/**
 * Class Module
 * @package Wave\Framework\Application
 */
class Module
{
    /**
     * @var Core
     */
    protected $application = null;

    /**
     * @var string
     */
    protected $prefix = null;

    /**
     * Creates new module for the current application
     *
     * @param $app Core Application instance
     * @param $name string Module name
     * @param $path string Path to the directory with XML routes file
     * @param $prefix string Route prefix of the routes
     */
    public function __construct(&$app, $name, $path, $prefix)
    {
        $this->application = $app;
        $this->prefix = $prefix;
        $self = $this;


        $app->controller(
            sprintf('%s(/.+)', $prefix),
            array('GET', 'POST', 'PUT', 'DELETE', 'CLI'),
            function () use (&$app, $name, $path, $self) {
                RouteFactory::build(sprintf("%s/%s.xml", $path, $name), $self);
            }
        );

    }

    /**
     * Wrapper for Core::controller
     *
     * @see Core::controller
     *
     * @param string $pattern
     * @param string|array $method
     * @param callable $callback
     * @param array  $conditions
     * @param string $handler
     *
     * @codeCoverageIgnore
     */
    public function controller(
        $pattern,
        $method,
        $callback,
        array $conditions = array(),
        $handler = '\Wave\Framework\Application\Controller'
    ) {
        $this->application->controller($this->prefix . $pattern, $method, $callback, $conditions, $handler);
    }
}
