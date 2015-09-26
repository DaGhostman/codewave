<?php
namespace Test\Application;

use Stub\MockUrl;
use Stub\StubRequest;
use Stub\StubResponse;
use Wave\Framework\Application\Router;
use Wave\Framework\Http\Request;
use Wave\Framework\Http\Response;
use Wave\Framework\Http\Url;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Router
     */
    private $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    public function testGet()
    {
        $this->router->get('/', [new \Stub\StubController, 'index']);
        $this->expectOutputString('true');

        $this->router->dispatch('GET', '/');

    }

    public function testPost()
    {
        $this->router->post('/', [new \Stub\StubController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch('POST', '/');
    }

    public function testPut()
    {
        $this->router->put('/', [new \Stub\StubController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch('PUT', '/');
    }

    public function testPatch()
    {
        $this->router->patch('/', [new \Stub\StubController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch('PATCH', '/');
    }

    public function testDelete()
    {
        $this->router->delete('/', [new \Stub\StubController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch('DELETE', '/');
    }

    /*
     * The PHP SAPI omits all output from the response, in other cases
     * this should be handled by the application developers. At present
     * the HEAD requests are an alias to GET routes.
     *
     */
    public function testHead()
    {
        $this->router->get('/', [new \Stub\StubController, 'index']);
        $this->expectOutputString('true'); // Verify the output
        $this->router->dispatch('HEAD', '/');
    }

    public function testOptions()
    {
        $this->router->options('/', [new \Stub\StubController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch('OPTIONS', '/');
    }

    public function testGroups()
    {
        $this->router->group(function($r) {
            /**
             * @var Router $r
             */
            $r->get('/stub', [new \Stub\StubController, 'index']);
        }, ['prefix' => '/test']);

        $this->expectOutputString('true');
        $this->router->dispatch('GET', '/test/stub');
    }

    public function testRouteGroupWithParameter()
    {
        $this->router->group(function($r) {
            $r->get('/test/uri', [new \Stub\StubController, 'index']);
        }, ['prefix' => '/group/{param}']);

        $this->expectOutputString('true');
        $this->router->dispatch('GET', '/group/hello/test/uri');
    }

    public function testNotFoundRoutes()
    {
        $this->router->get('/some/uri', []);
        $this->setExpectedException('\Wave\Framework\Exceptions\HttpNotFoundException');
        $this->router->dispatch('GET', '/');
    }

    public function testNotAllowedRoutes()
    {
        $this->router->post('/', ['Stub\Controller', 'index']);
        $this->setExpectedException('\Wave\Framework\Exceptions\HttpNotAllowedException');
        $this->router->dispatch('GET', '/');
    }

    public function testNamedRoutes()
    {
        $this->router->get('/test/uri/with/{param}/regexParam/{regexParam:[a-c]+}', [new \Stub\StubController, 'index'], 'testRoute');

        $this->assertSame(
            '/test/uri/with/simpleParameter/regexParam/aabc',
            $this->router->route('testRoute', [
                'param' => 'simpleParameter',
                'regexParam' => 'aabc'
            ])
        );
        $this->expectOutputString('true');
        $this->router->dispatch('GET', '/test/uri/with/simpleParameter/regexParam/aabc');
    }

    public function testNamedRoutesFromGroups()
    {
        $this->router->group(function($r) {
            $r->get('/some/{param}', [new \Stub\StubController, 'index'], 'test');
        }, ['prefix' => '/test/{uri}/with/{params}']);

        $this->assertSame(
            '/test/route/with/parameters/some/parameter',
            $this->router->route('test', [
                'params' => 'parameters',
                'param' => 'parameter',
                'uri' => 'route'
            ])
        );

        $this->expectOutputString('true');
        $this->router->dispatch('GET', '/test/route/with/parameters/some/parameter');
    }

    public function testNamedRoutesLogicException()
    {
        $this->router
            ->get('/some/uri', [], 'test');

        $this->setExpectedException(
            '\LogicException',
            'Number of arguments does not match the number of bound parameters'
        );
        $this->router->route('test', ['param' => 'Hello+World!']);
    }

    public function testNamedRoutesArgumentException()
    {
        $this->router
            ->get('/some/{param}', [new \Stub\StubController(), 'index'], 'test');

        $this->setExpectedException(
            '\LogicException',
            'Number of arguments does not match the number of bound parameters'
        );

        $this->router->route('test', ['some' => 'param']);
    }

    public function testNamedRouteNotFoundException()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Route with name "notFound" does not exist'
        );
        $this->router->route('notFound', []);
    }
}
