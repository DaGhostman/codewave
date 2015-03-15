<?php

namespace Test\Application;


use Stub\Application\RequestStub;
use Wave\Framework\Application\Server;
use Wave\Framework\Http\Server\Response;

class ServerTest extends \PHPUnit_Framework_TestCase {
    private $srv = null;

    protected function setUp()
    {
        $req = new RequestStub('GET', '/');
        $this->srv = new Server($req, null);
    }

    public function testInvalidRequestObject()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid request');
        new Server(new \stdClass(), null);
    }

    public function testFunctionResultAsString()
    {
        $srv = new Server(new RequestStub('/', 'GET'), new Response());
        $r = $srv->dispatch(function($req, $resp) {
            return $resp;
        });

        $this->assertInstanceOf('\Wave\Framework\Http\Server\Response', $r);
    }

    public function testFunctionReturningStringWithoutResponse()
    {
        $this->setExpectedException('\RuntimeException', 'No response object defined');
        $r = $this->srv->dispatch(function($req, $resp) {
            return 'Hello, World';
        });

        $this->assertInstanceOf('\Wave\Framework\Http\Server\Response', $r);
    }

    public function testBadCallbackReturn()
    {
        $this->setExpectedException('\RuntimeException', 'Expected string or instance of Response');
        $this->srv->dispatch(function () {return new \stdClass();});
    }

    public function testOutputString()
    {
        $this->assertNull($this->srv->send());
    }
}
