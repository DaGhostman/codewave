<?php
namespace Test\Application;

use Stub\StubRequest;
use Stub\StubResponse;
use Wave\Framework\Application\Router;
use Zend\Diactoros\Uri;

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

    private $mockController;

    public function setUp()
    {
        $this->mockController = $this->getMockBuilder('\stdClass')
            ->setMethods(['index'])
            ->getMock();

        $this->mockController->expects($this->any())
            ->method('index')
            ->willReturnCallback(function () {
                echo 'true';
            });

        $this->request = new StubRequest();
        $this->response = new StubResponse();

        $this->router = new Router();
    }

    public function testGet()
    {
        $this->router->get('/', [$this->mockController, 'index']);
        $this->expectOutputString('true');

        $this->router->dispatch($this->request->withMethod('GET')->withUri(new Uri('/')), $this->response);

    }

    public function testPost()
    {
        $this->router->post('/', [$this->mockController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch($this->request->withMethod('POST')->withUri(new Uri('/')), $this->response);
    }

    public function testPut()
    {
        $this->router->put('/', [$this->mockController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch($this->request->withMethod('PUT')->withUri(new Uri('/')), $this->response);
    }

    public function testPatch()
    {
        $this->router->patch('/', [$this->mockController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch($this->request->withMethod('PATCH')->withUri(new Uri('/')), $this->response);
    }

    public function testDelete()
    {
        $this->router->delete('/', [$this->mockController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch($this->request->withMethod('DELETE')->withUri(new Uri('/')), $this->response);
    }

    /*
     * The PHP SAPI omits all output from the response, in other cases
     * this should be handled by the application developers. At present
     * the HEAD requests are an alias to GET routes.
     *
     */
    public function testHead()
    {
        $this->router->get('/', [$this->mockController, 'index']);
        $this->expectOutputString('true'); // Verify the output
        $this->router->dispatch($this->request->withMethod('HEAD')->withUri(new Uri('/')), $this->response);
    }

    public function testOptions()
    {
        $this->router->options('/', [$this->mockController, 'index']);
        $this->expectOutputString('true');
        $this->router->dispatch($this->request->withMethod('OPTIONS')->withUri(new Uri('/')), $this->response);
    }

    public function testGroups()
    {
        $this->router->group(function($r) {
            /**
             * @var Router $r
             */
            $r->get('/stub', [$this->mockController, 'index']);
        }, ['prefix' => '/test']);

        $this->expectOutputString('true');
        $this->router->dispatch($this->request->withMethod('GET')->withUri(new Uri('/test/stub')), $this->response);
    }

    public function testRouteGroupWithParameter()
    {
        $this->router->group(function($r) {
            $r->get('/test/uri', [$this->mockController, 'index']);
        }, ['prefix' => '/group/{param}']);

        $this->expectOutputString('true');
        $this->router->dispatch($this->request->withMethod('GET')->withUri(new Uri('/group/hello/test/uri')),
            $this->response);
    }

    public function testInvalidCallable()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Provided Handler not callable');
        $this->router->get('/', []);
    }

    public function testNotFoundRoutes()
    {
        $this->router->get('/some/uri', [$this->mockController, 'index']);
        $this->setExpectedException('\Wave\Framework\Exceptions\Dispatch\NotFoundException');
        $this->router->dispatch($this->request->withMethod('GET'), $this->response);
    }

    public function testNotAllowedRoutes()
    {
        $this->router->post('/', [$this->mockController, 'index']);
        $this->setExpectedException('\Wave\Framework\Exceptions\Dispatch\MethodNotAllowedException');
        $this->router->dispatch($this->request->withMethod('GET')->withUri(new Uri('/')), $this->response);
    }

    public function testNamedRoutes()
    {
        $this->router->get('/test/uri/with/{param}/regexParam/{regexParam:[a-c]+}', [$this->mockController, 'index'],
            null, 'testRoute');

        $this->assertSame(
            '/test/uri/with/simpleParameter/regexParam/aabc',
            $this->router->route('testRoute', [
                'param' => 'simpleParameter',
                'regexParam' => 'aabc'
            ])
        );
        $this->expectOutputString('true');
        $this->router->dispatch(
            $this->request->withMethod('GET')
                ->withUri(new Uri('/test/uri/with/simpleParameter/regexParam/aabc')),
            $this->response
        );
    }

    public function testNamedRoutesFromGroups()
    {
        $this->router->group(function($r) {
            $r->get('/some/{param}', [$this->mockController, 'index'], null, 'test');
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
        $this->router->dispatch(
            $this->request->withMethod('GET')
                ->withUri(new Uri('/test/route/with/parameters/some/parameter')),
            $this->response
        );
    }

    public function testNamedRoutesLogicException()
    {
        $this->router
            ->get('/some/uri', [$this->mockController, 'index'], null, 'test');

        $this->setExpectedException(
            '\LogicException',
            'Number of arguments does not match the number of bound parameters'
        );
        $this->router->route('test', ['param' => 'Hello+World!']);
    }

    public function testNamedRoutesArgumentException()
    {
        $this->router
            ->get('/some/{param}', [$this->mockController, 'index'], null, 'test');

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
