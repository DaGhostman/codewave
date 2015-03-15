<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 02/02/15
 * Time: 00:31
 */
namespace Test\Application;

use Stub\Application\RequestStub;
use Wave\Framework\Application\Wave;
use Wave\Framework\Http\Server\Response;

class WaveTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Wave
     */
    private $app = null;
    private $response = null;

    protected function setUp()
    {
        $this->app = new Wave([]);
        $this->response = new Response();
    }

    public function testSimpleRouting()
    {
        $this->expectOutputString('Routes / called');
        
        $request = new RequestStub('GET', '/');
        
        $this->app->get('/', function ()
        {
            return 'Routes / called';
        });
        $this->app->run($request, $this->response);
    }

    public function testUriWithParameters()
    {
        $request = new RequestStub('GET', '/greet/ghost');
        $this->expectOutputString('Hello, ghost');
        $this->app->get('/greet/{name}', function ($request)
        {
            return sprintf('Hello, %s', $request->name);
        });
        $this->app->run($request, $this->response);
    }

    public function testUriWithParametersWithPattern()
    {
        $this->expectOutputString('OK');
        
        $request = new RequestStub('GET', '/greet/ghost-');
        $this->app->get('/greet/{name:a}', function ()
        {
            echo 'Should not invoke';
        });
        
        $this->app->run($request, $this->response);
        echo 'OK';
    }

    public function test404Handler()
    {
        $this->expectOutputString('404 Not Found');
        $this->app->setNotFoundHandler(function ()
        {
            echo '404 Not Found';
        });
        $this->app->run(new RequestStub('GET', '/'), $this->response);
    }

    public function testNotAllowedHandler()
    {
        $this->expectOutputString('Not Allowed');
        $this->app->setNotAllowedHandler(function ()
        {
            echo 'Not Allowed';
        });
        $this->app->post('/', function ()
        {
            return 0;
        });
        $this->app->run(new RequestStub('GET', '/'), $this->response);
    }

    public function testGetRoute()
    {
        $this->assertInstanceOf('\Phroute\Phroute\RouteCollector', $this->app->getRouter());
    }

}
