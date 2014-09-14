<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 15/08/14
 * Time: 23:17
 */

namespace Tests\Application;

use Wave\Framework\Application\Core;

class ControllerStub {
    public function action(){}
    public function throwException(){throw new \RuntimeException("It works!");}
}

class RequestStub {
    private $uri = null;
    private $method = null;

    public function __construct($uri = '/', $method = 'GET') {
        $this->uri = $uri;
        $this->method = $method;
    }

    public function uri() { return $this->uri; }
    public function method() { return $this->method; }
}


class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testControllerInvoke()
    {
        $this->expectOutputString('RUN');

        $app = new Core;
        $app->controller('/', 'GET', function () {
            print "RUN";
        });
        $app->run(new RequestStub(), null, array());
        $this->assertSame(1, $app->numControllers());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testControllerDefinitionWithCustomHandler()
    {
        $app = new Core;
        $app->controller('/', 'GET', function(){}, array(), '\stdClass');
    }

    public function testControllerInvokeWithParam()
    {

        $this->expectOutputString('RUN Ghost');

        $app = new Core;
        $app->controller('/:name+', 'GET', function ($arguments, $context = array()) {
            $name = $arguments->get('name');

            print 'RUN '.$name[0];
        });

        $app->run(new RequestStub('/Ghost/man'), null, array());
        $this->assertSame(1, $app->numControllers());
    }

    public function testControllerInvokeWithContextData()
    {
        $this->expectOutputString('Running');
        $app = new Core();
        $app->controller('/', 'GET', function($arguments, $context) {

            print $context['action'];
        });

        $app->run(new RequestStub(), null, array('action' => 'Running'));
        $this->assertSame(1, $app->numControllers());
    }

    public function testControllerInvokeNonEnglishUri()
    {
        $this->expectOutputString('Здравей, Свят');

        $app = new Core();
        $app->controller('/:greet', 'GET', function($args, $context) {
            print sprintf('Здравей, %s', $args->get('greet'));
        });

        $app->run(new RequestStub('/Свят'));
        $this->assertSame(1, $app->numControllers());
    }

    public function testControllerInvokeWithConditions()
    {
        $this->expectOutputString('');
        $app = new Core();
        $app->controller('/:name', 'GET', function ($arguments, $context) {
            print "Not Called";
        }, array(
            'name' => '[a-z]{1,}'
        ));

        $app->run(new RequestStub());
        $this->assertSame(1, $app->numControllers());
    }


    public function testControllerInvokeWithMatchedConditions()
    {
        $this->expectOutputString('Ghost');
        $app = new Core();
        $app->controller('/:name', 'GET', function($arguments, $context) {
            print $arguments->get('name');
        }, array(
            'name' => '[a-z]{1,}'
        ));

        $app->run(new RequestStub('/Ghost'));
        $this->assertSame(1, $app->numControllers());
    }

    public function testControllerInvokeWithMultimethodCall()
    {
        $this->expectOutputString('NameName');
        $app = new Core();
        $app->controller('/', array('GET', 'POST'), function() {
            echo "Name";
        });

        $app->run(new RequestStub('/', 'GET'));
        $app->run(new RequestStub('/', 'POST'));
        $this->assertSame(1, $app->numControllers());
    }


    public function testControllerInvokeWithDebugging()
    {
        $this->expectOutputRegex('#^[Error occurred\: "It works"]+#i');

        $app = new Core();
        $app->debug()
            ->controller('/', 'GET', array(
                '\Tests\Application\ControllerStub',
                'throwException'
            ));

        $app->run(new RequestStub(), array());
        $this->assertSame(1, $app->numControllers());
    }

    public function testManualIteration()
    {
        $app = new Core;

        $app->controller('/', 'GET', function(){});

        $this->assertNull($app->rewind());
        $this->assertTrue($app->valid());
        $this->assertInstanceOf('\Wave\Framework\Application\Controller', $app->current());
        $this->assertSame(0, $app->key());
    }

    public function testControllerClear()
    {
        $app = new Core;
        $app->controller('/', 'GET', function(){});
        $app->controller('/foo', 'GET', function(){});
        $app->controller('/bar', 'GET', function(){});

        $this->assertSame(3, count($app));
        $app->clearControllers();
        $this->assertSame(0, count($app));
    }

    public function testRedirection()
    {
        $this->expectOutputString('Redirected');
        $app = new Core;
        $app->controller('/test', 'GET', function () {
            echo 'Redirected';
        });
        $app->controller('/', 'GET', function () use ($app) {
            $app->redirect('/test');
        });
        $app->run(new RequestStub());
    }

    public function test404Callback()
    {
        $this->expectOutputString('Not Found');
        $app = new Core;
        $app->notFound('/404', function () {
            echo 'Not Found';
        });
        $app->controller('/why', 'GET', function () {
            echo 'Why?';
        });
        $app->run(new RequestStub());
    }
}
