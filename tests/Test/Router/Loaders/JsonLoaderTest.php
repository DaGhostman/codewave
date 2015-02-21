<?php
namespace Test\Router;

use Stub\Router\Router;
use Wave\Framework\Router\Loaders\JsonLoader;

class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{

    private $route = null;

    private $loader = null;

    protected function setUp()
    {
        $this->route = new Router();
        $this->loader = new JsonLoader('[{"method": "GET", "uri": "/", "callback": "dummyFunction"}]');
    }

    public function testBasicRouteCreation()
    {
        $this->loader->pushRoutes($this->route);
        
        $this->assertSame([
            [
                "GET",
                "/",
                "dummyFunction"
            ]
        ], $this->route->getArray());
    }
}
