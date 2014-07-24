<?php

namespace Tests;

use Wave\Application\Loader;

class StubController
{
    public function stubAction()
    {
        echo "Foo";
    }
}

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    private $loader = null;
    protected function setUp()
    {
        $this->loader = new Loader(array(
            'environment' => array(
                'request.protocol' => 'HTTP/1.1',
                'request.port' => 80,
                'hostname' => 'localhost',
                'request.uri' => '/',
                'request.method' => 'GET'
            ),
            'debug' => true
        ));
    }

    public function testGetter()
    {
        $this->assertNull($this->loader->some_variable);
        $this->assertTrue($this->loader->config('debug'));

        $this->assertInstanceOf('\Wave\Http\Request', $this->loader->http('request'));
        $this->assertNull($this->loader->http('notExist'));

        $this->assertNull($this->loader->config('model'));
    }

    public function testCaller()
    {
        $this->assertNull($this->loader->some_function());
        $this->assertInstanceOf(
            '\Wave\Storage\Registry',
            $this->loader->environement()
        );
    }
    
    public function testConstructionOfObject()
    {
        $loader = new Loader(array(
            'environment' => array(
                'request.protocol' => 'HTTP/1.1',
                'request.port' => 80,
                'hostname' => 'localhost',
                'request.uri' => '/bar',
                'request.method' => 'GET'
            )
        ));
        $loader->bootstrap();
        
        $this->assertInstanceOf('\Wave\Storage\Registry', $loader->environement);
        $this->assertInstanceOf('\Wave\Http\Factory', $loader->http);
        $this->assertInstanceOf('\Wave\Http\Request', $loader->http->request());
        $this->assertInstanceOf('\Wave\Http\Response', $loader->http->response());
    }
    
    public function testRawRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;
        
        $ref = new \ReflectionObject($loader);
        $method = $ref->getMethod('mapRoute');
        $method->setAccessible(true);
        
        $ret = $method->invoke($loader, array('/', function () {
                return null;
        }));
        
        $this->assertInstanceOf('\Wave\Application\Route', $ret);
        $this->assertEmpty($ret->getHttpMethods());
    }

    public function testMapRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;

        $route = $loader->map('/', function () {
            return null;
        })->via('GET', 'HEAD');

        $this->assertInstanceOf('\Wave\Application\Route', $route);
        $this->assertSame(array('GET','HEAD'), $route->getHttpMethods());
    }
    
    public function testGetRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;
    
        $route = $loader->get('/', function () {
            return null;
        });
    
        $this->assertInstanceOf('\Wave\Application\Route', $route);
        $this->assertSame(array('GET'), $route->getHttpMethods());
    }
    
    public function testPostRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;
    
        $route = $loader->post('/', function () {
            return null;
        });
    
        $this->assertInstanceOf('\Wave\Application\Route', $route);
        $this->assertSame(array('POST'), $route->getHttpMethods());
    }
    
    public function testPutRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;
    
        $route = $loader->put('/', function () {
            return null;
        });
    
        $this->assertInstanceOf('\Wave\Application\Route', $route);
        $this->assertSame(array('PUT'), $route->getHttpMethods());
    }
    
    public function testOptionsRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;
    
        $route = $loader->options('/', function () {
            return null;
        });
    
        $this->assertInstanceOf('\Wave\Application\Route', $route);
        $this->assertSame(array('OPTIONS'), $route->getHttpMethods());
    }
    
    public function testTraceRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;
    
        $route = $loader->trace('/', function () {
            return null;
        });
    
        $this->assertInstanceOf('\Wave\Application\Route', $route);
        $this->assertSame(array('TRACE'), $route->getHttpMethods());
    }
    
    public function testDeleteRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;
    
        $route = $loader->delete('/', function () {
            return null;
        });
    
        $this->assertInstanceOf('\Wave\Application\Route', $route);
        $this->assertSame(array('DELETE'), $route->getHttpMethods());
    }
    
    public function testConnectRouteGeneration()
    {
        $this->loader->bootstrap();
        $loader = $this->loader;
    
        $route = $loader->connect('/', function () {
            return null;
        });
    
        $this->assertInstanceOf('\Wave\Application\Route', $route);
        $this->assertSame(array('CONNECT'), $route->getHttpMethods());
    }
    
    public function testRun()
    {
        $this->expectOutputString('1.2.Foo.3');
        
        echo '1.';
        
        $loader = new Loader(array(
            'environment' => array(
                'request.protocol' => 'HTTP/1.1',
                'request.port' => 80,
                'hostname' => 'localhost',
                'request.uri' => '/bar',
                'request.method' => 'GET'
            ),
            'debug' => true
        ));
        
        $loader->bootstrap();
        
        $loader->get('/bar', function () {
            echo 'Foo';
            //throw new \Wave\Application\State\Halt();
            return true;
        });
        echo '2.';
        $loader->run();
        echo '.3';
    }

    public function testRunConstructRoutes()
    {
        $this->expectOutputString('1.2.Foo.3');

        echo '1.';

        $loader = new Loader(array(
            'environment' => array(
                'request.protocol' => 'HTTP/1.1',
                'request.port' => 80,
                'hostname' => 'localhost',
                'request.uri' => '/bar',
                'request.method' => 'GET'
            ),
            'debug' => true
        ), array(array(
            'pattern' => '/:bar',
            'callback' => '\Tests\StubController:stubAction',
            'method' => array('GET'),
            'name' => 'stub',
            'conditions' => array('bar' => 'bar')
        )));

        $loader->bootstrap();

        echo '2.';
        $loader->run();
        echo '.3';
    }
    
    public function testRunWithPass()
    {
        $this->expectOutputString('1.2.Foo.Bar.3');
    
        echo '1.';
    
        $loader = new Loader(array(
            'environment' => array(
                'request.protocol' => 'HTTP/1.1',
                'request.port' => 80,
                'hostname' => 'localhost',
                'request.uri' => '/bar',
                'request.method' => 'GET'
            )
        ));
    
        $loader->bootstrap();
    
        $loader->get('/bar', function () {
            echo 'Foo';
            throw new \Wave\Application\State\Pass();
            return true;
        });
        $loader->get('/bar', function () {
            echo '.Bar';
            throw new \Wave\Application\State\Halt();
            return true;
        });

        echo '2.';
        $loader->run();
        echo '.3';
    }
    
    public function testRunWithHalt()
    {
        $this->expectOutputString('1.2.Foo.3');
    
        echo '1.';
    
        $loader = new Loader(array(
            'environment' => array(
                'request.protocol' => 'HTTP/1.1',
                'request.port' => 80,
                'hostname' => 'localhost',
                'request.uri' => '/bar',
                'request.method' => 'GET'
            )
        ));
    
        $loader->bootstrap();
    
        $loader->get('/bar', function () {
            echo 'Foo';
            throw new \Wave\Application\State\Halt();
            return true;
        });

        echo '2.';
        $loader->run();
        echo '.3';
    }
    
    /**
     * 
     * @expectedException RuntimeException
     */
    public function testRunWithException()
    {
        
    
        $loader = new Loader(array(
            'environment' => array(
                'request.protocol' => 'HTTP/1.1',
                'request.port' => 80,
                'hostname' => 'localhost',
                'request.uri' => '/bar',
                'request.method' => 'GET'
            ),
            'debug' => true
        ));
    
    
        $loader->get('/bar', function () {
            throw new \RuntimeException('Works!');
        });
        
        $loader->run();
    }

    public function testViewerAssign()
    {
        $app = new Loader(array('environment' => array()));
        $app->setView(new \stdClass());

        $this->assertInstanceOf('\stdClass', $app->view);
        $this->assertInstanceOf('\stdClass', $app->view());
    }
}
