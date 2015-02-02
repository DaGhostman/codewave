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

class WaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Wave
     */
    private $app = null;
    protected function setUp()
    {
        $this->app = new Wave([]);
    }

    public function testSimpleRouting()
    {
        $this->expectOutputString('Route / called');

        $request = new RequestStub('GET', '/');

        $this->app->get('/', function () {
            echo 'Route / called';
        });
        $this->app->run($request);
    }

    public function testUriWithParameters()
    {
        $request = new RequestStub('GET', '/greet/ghost');
        $this->expectOutputString('Hello, ghost');
        $this->app->get('/greet/{name}', function ($name) {
            echo sprintf('Hello, %s', $name);
        });
        $this->app->run($request);
    }


    public function testUriWithParametersWithPattern()
    {
        $this->expectOutputString('OK');

        $request = new RequestStub('GET', '/greet/ghost-');
        $this->app->get('/greet/{name:a}', function () {
            echo 'Should not invoke';
        });

        $this->app->run($request);
        echo 'OK';
    }

    public function test404Handler()
    {
        $this->expectOutputString('404 Not Found');
        $this->app->setNotFoundHandler(function () {
            echo '404 Not Found';
        });
        $this->app->run(new RequestStub('GET', '/'));
    }

    public function testNotAllowedHandler()
    {
        $this->expectOutputString('Not Allowed');
        $this->app->setNotAllowedHandler(function () {
            echo 'Not Allowed';
        });
        $this->app->post('/', function () {
            return 0;
        });
        $this->app->run(new RequestStub('GET', '/'));
    }
}
