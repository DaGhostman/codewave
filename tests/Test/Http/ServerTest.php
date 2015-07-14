<?php


namespace Test\Http;


use Stub\MockUrl;
use Stub\StubMiddleware;
use Stub\StubRequest;
use Stub\StubResponse;
use Stub\StubRouter;
use Wave\Framework\Http\Server;
use Wave\Framework\Http\Url;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Server
     */
    private $server;
    protected function setUp()
    {
        $request = new StubRequest('GET', new MockUrl());
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
        $this->server->listen(new StubRouter('Hello, World!'));
    }

    public function testWithMiddleware()
    {
        $this->expectOutputString('Begin-Application-End');
        $this->server->addMiddleware(new StubMiddleware());
        $this->server->listen(new StubRouter('Application'));
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

    public function testTrace()
    {
        $r = (new StubRequest('TRACE', new Url()))
            ->addHeader('Content-type', 'application/json')
            ->addHeader('User-Agent', 'PHPUnit-Test');

        $this->expectOutputString("Content-Type: application/json\n\rUser-Agent: PHPUnit-Test\n\r");
        $server = new Server($r, new StubResponse());
        $server->listen(new StubRouter('true'));
    }
}
