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
}
