<?php

namespace Test\Application;


use Stub\Application\RequestStub;
use Wave\Framework\Http\Server;
use Wave\Framework\Http\Response;

class ServerTest extends \PHPUnit_Framework_TestCase {
    private $srv = null;

    protected function setUp()
    {
        $req = new RequestStub('GET', '/');
        $this->srv = new Server($req);
    }

    public function testInvalidRequestObject()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new Server(new \stdClass());
    }

    public function testFunctionResultAsString()
    {
        $this->expectOutputString('Hello, World');
        $srv = new Server(new RequestStub('/', 'GET'));
        $srv->listen(function () {
            return 'Hello, World';
        })->send();
    }

    public function testBadCallbackReturn()
    {
        $this->setExpectedException('\RuntimeException', 'Unexpected result type "object"');
        $this->srv->listen(function () {return new \stdClass();});
    }

    public function testOutputString()
    {
        $this->assertNull($this->srv->send());
    }
}
