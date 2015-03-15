<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 02/02/15
 * Time: 00:31
 */
namespace Test\Application;

use Wave\Framework\Application\Wave;

class WaveTest extends \PHPUnit_Framework_TestCase
{
    private $app = null;

    protected function setUp()
    {
        $this->app = new Wave([]);
    }

    public function testSimpleRouting()
    {
        $this->expectOutputString('Routes / called');
        
        $this->app->get('/', function ()
        {
            return 'Routes / called';
        });
        $this->app->run(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET']);
    }

    public function testUriWithParameters()
    {
        $this->expectOutputString('Hello, ghost');
        $this->app->get('/greet/{name}', function ($request)
        {
            return sprintf('Hello, %s', $request->name);
        });
        $this->app->run(['REQUEST_URI' => '/greet/ghost', 'REQUEST_METHOD' => 'GET']);
    }

    public function testUriWithParametersWithPattern()
    {
        $this->expectOutputString('OK');

        $this->app->get('/greet/{name:a}', function ()
        {
            echo 'Should not invoke';
        });
        
        $this->app->run([
            'REQUEST_URI' => '/greet/ghost-',
            'REQUEST_METHOD' => 'GET'
        ]);
        echo 'OK';
    }

    public function test404Handler()
    {
        $this->expectOutputString('404 Not Found');
        $this->app->setNotFoundHandler(function ()
        {
            echo '404 Not Found';
        });
        $this->app->run(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET']);
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
        $this->app->run(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET']);
    }

    public function testGetRoute()
    {
        $this->assertInstanceOf('\Phroute\Phroute\RouteCollector', $this->app->getRouter());
    }

}
