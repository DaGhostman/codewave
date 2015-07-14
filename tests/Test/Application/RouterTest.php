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

    /**
     * @var StubRequest
     */
    private $request;

    /**
     * @var StubResponse
     */
    private $response;
    public function setUp()
    {
        $url = new MockUrl('/');
        $this->request = new StubRequest('GET', $url, []);
        $this->response = new StubResponse();
        $this->router = new Router();
    }

    public function testGet()
    {
        $this->router->get('/', ['\Stub\StubController', 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch($this->request, $this->response);
    }

    public function testPost()
    {
        $this->router->post('/', ['\Stub\StubController', 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch(new StubRequest('POST', new Url()), $this->response);
    }

    public function testPut()
    {
        $this->router->put('/', ['\Stub\StubController', 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch(new StubRequest('PUT', new Url()), $this->response);
    }

    public function testPatch()
    {
        $this->router->patch('/', ['\Stub\StubController', 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch(new StubRequest('PATCH', new Url()), $this->response);
    }

    public function testDelete()
    {
        $this->router->delete('/', ['\Stub\StubController', 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch(new Request('DELETE', new Url()), $this->response);
    }

    /*
     * The PHP SAPI omits all output from the response, in other cases
     * this should be handled by the application developers. At present
     * the HEAD requests are an alias to GET routes.
     *
     */
    public function testHead()
    {
        $this->router->get('/', ['\Stub\StubController', 'index']);
        $this->expectOutputString('true'); // Verify the output
        $this->router->dispatch(new Request('HEAD', new Url()), $this->response);
    }

    public function testOptions()
    {
        $this->router->options('/', ['\Stub\StubController', 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch(new Request('OPTIONS', new Url()), $this->response);
    }

    public function testGroups()
    {
        $this->router->group(function($r) {
            /**
             * @var Router $r
             */
            $r->get('/stub', ['Stub\StubController', 'index']);
        }, ['prefix' => '/test']);

        $this->expectOutputString('true');
        $this->router->dispatch(new Request('GET', new Url('/test/stub')), new StubResponse());
    }

    public function testRouteGroupWithParameter()
    {
        $this->router->group(function($r) {
            $r->get('/test/uri', ['Stub\StubController', 'index']);
        }, ['prefix' => '/group/{param}']);

        $this->expectOutputString('true');
        $this->router->dispatch(new StubRequest('GET', new Url('/group/hello/test/uri')), new StubResponse());
    }

    public function testNotFoundRoutes()
    {
        $this->router->get('/some/uri', ['Stub\Controller', 'index']);
        $this->setExpectedException('\Wave\Framework\Exceptions\HttpNotFoundException');
        $this->router->dispatch($this->request, new StubResponse());
    }

    public function testNotAllowedRoutes()
    {
        $this->router->post('/', ['Stub\Controller', 'index']);
        $this->setExpectedException('\Wave\Framework\Exceptions\HttpNotAllowedException');
        $this->router->dispatch($this->request, new StubResponse());
    }

    public function testNamedRoutes()
    {
        $this->router->post('/test/uri/with/{param}/regexParam/{regexParam:[a-c]+}', [], 'testRoute');

        $this->assertSame(
            '/test/uri/with/simpleParameter/regexParam/aabc',
            $this->router->route('testRoute', [
                'param' => 'simpleParameter',
                'regexParam' => 'aabc'
            ])
        );
    }

    public function testNamedRoutesFromGroups()
    {
        $this->router->group(function($r) {
            $r->get('/some/{param}', [], 'test');
        }, ['prefix' => '/test/{uri}/with/{params}']);

        $this->assertSame(
            '/test/route/with/parameters/some/parameter',
            $this->router->route('test', [
                'params' => 'parameters',
                'param' => 'parameter',
                'uri' => 'route'
            ])
        );
    }

    public function testNamedRoutesLogicException()
    {
        $this->router
            ->get('/some/uri', [], 'test');

        $this->setExpectedException('\LogicException');
        $this->router->route('test', ['param' => 'Hello+World!']);
    }

    public function testNamedRoutesArgumentException()
    {
        $this->router
            ->get('/some/{param}', [], 'test');

        $this->setExpectedException('\LogicException');
        $this->router->route('test', ['some' => 'param']);
    }

    public function testNamedRouteNotFoundException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->router->route('notFound', []);
    }
}
