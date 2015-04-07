<?php

namespace Wave\Framework\External\Plates\Extensions\Phroute;

use \League\Plates\Engine;
use \League\Plates\Extension\ExtensionInterface;
use \Phroute\Phroute\RouteCollector;

class URIBuilder implements ExtensionInterface
{
    /**
     * @type RouteCollector
     */
    private $router = null;

    public function __construct($router)
    {
        if (!$router instanceof RouteCollector) {
            throw new \InvalidArgumentException(sprintf(
                'Argument should be instance of \Phroute\Phroute\RouteCollector, "%s" received',
                gettype($router)
            ));
        }

        $this->router = $router;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('buildURI', [$this, 'buildRoute']);
    }

    public function buildRoute($name, $args)
    {
        return $this->router->route($name, $args);
    }
}