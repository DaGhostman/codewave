<?php


namespace Test\Http;


use Stub\StubMiddleware;
use Stub\StubRequest;
use Stub\StubResponse;
use Wave\Framework\Http\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    private $server;
    protected function setUp()
    {
        $request = new StubRequest();
        $response = new StubResponse();

        $raw = [
            'HTTP_USER_AGENT' => 'PHP',
            'CONTENT_TYPE' => 'text/plain',
            'HTTP_COOKIE' => 'hello=world;'
        ];

        $this->server = new Server($request, $response, $raw);
    }

    public function testListen()
    {
        $this->expectOutputString('Hello, World!');
        $this->server->listen(function() {
            echo 'Hello, World!';
        });
    }

    public function testWithMiddleware()
    {
        $this->expectOutputString('Begin-Application-End');
        $this->server->addMiddleware(new StubMiddleware());
        $this->server->listen(function() { echo 'Application'; });
    }

    public function testRequestHeadersParsing()
    {
        $this->assertInstanceOf('\Stub\StubRequest', $this->server->getRequest());
        $req = $this->server->getRequest();
        $this->assertSame('text/plain', $req->getHeader('Content-Type'));
        $this->assertNull($req->getHeader('Cookie'));
    }

    public function testResponse()
    {
        $this->assertInstanceOf('\Stub\StubResponse', $this->server->getResponse());
    }
}
