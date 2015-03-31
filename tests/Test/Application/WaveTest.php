<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 02/02/15
 * Time: 00:31
 */
namespace Test\Http;

use Wave\Framework\Application\Wave;

class WaveTest extends \PHPUnit_Framework_TestCase
{
    private $app = null;
    private $factory = null;

    protected function setUp()
    {
        $this->app = new Wave(function() {});
    }


    public function testUriWithParameters()
    {
        $router = \Mockery::mock('\Phroute\Phroute\RouteCollector')
            ->shouldReceive('get')->once()->andReturnSelf();

        $this->app->setRouter($router->getMock());

        $this->assertEquals($router->getMock(), $this->app->get('/greet/{name}', function ($request)
        {
            return sprintf('Hello, %s', $request->name);
        }));
    }
}
