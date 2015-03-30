<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 02/02/15
 * Time: 00:31
 */
namespace Test\Http;

use Stub\Application\RequestStub;
use Wave\Framework\Application\Wave;
use Wave\Framework\Factory\Server;

class WaveTest extends \PHPUnit_Framework_TestCase
{
    private $app = null;
    private $factory = null;

    protected function setUp()
    {
        $dispatcher = new Mockery;

        $this->app = new Wave(function() {
            var_dump(func_get_args());
        });
        $this->app->setRouter(new \stdClass);

        //$server = ['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET', 'HTTP_HOST' => 'localhost'];
    }


    public function testUriWithParameters()
    {
        $this->expectOutputString('Hello, ghost');
        $this->app->get('/greet/{name}', function ($request)
        {
            return sprintf('Hello, %s', $request->name);
        });
        $this->app->run(new RequestStub('GET', '/greet/ghost'));
    }

    public function testUriWithParametersWithPattern()
    {
        $this->expectOutputString('OK');

        $this->app->get('/greet/{name:a}', function ()
        {
            echo 'Should not invoke';
        });
        
        $this->app->run(new Server([
            'REQUEST_URI' => '/greet/ghost-',
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'localhost'
        ]));
        echo 'OK';
    }

    public function test404Handler()
    {
        $this->expectOutputString('404 Not Found');
        $this->app->setNotFoundHandler(function ()
        {
            echo '404 Not Found';
        });

        $this->app->run($this->factory);
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
        $this->app->run($this->factory);
    }

    public function testGetRoute()
    {
        $this->assertInstanceOf('\Phroute\Phroute\RouteCollector', $this->app->getRouter());
    }

}
