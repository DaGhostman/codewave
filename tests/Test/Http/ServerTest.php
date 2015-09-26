<?php


namespace Test\Http;


use Stub\StubMiddleware;
use Stub\StubRequest;
use Stub\StubResponse;
use Wave\Framework\Http\Server;
use Wave\Framework\Http\Url;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRouter;

    public function testListen()
    {
        $this->expectOutputString('Hello, World!');
        $this->server->listen($this->mockRouter);
    }

    public function testWithMiddleware()
    {
        $this->expectOutputString('Begin-Hello, World!-End');
        $this->server->addMiddleware(new StubMiddleware());
        $this->server->listen($this->mockRouter);
    }

    public function testRequestHeadersParsing()
    {
        $req = $this->server->getRequest();
        $this->assertSame('text/plain', $req->getHeader('Content-Type'));
        $this->assertNull($req->getHeader('Cookie'));
    }

    public function testTrace()
    {
        $r = (new StubRequest('TRACE', new Url()))
            ->addHeader('Content-type', 'application/json')
            ->addHeader('User-Agent', 'PHPUnit-Test');

        $this->expectOutputString("Content-Type: application/json\n\rUser-Agent: PHPUnit-Test\n\r");
        $server = new Server($r, new StubResponse());
        $server->listen($this->mockRouter);
    }

    /*
     * @ToDo: Rewrite test
     */

    protected function setUp()
    {

        $mockUrl = $this->getMockBuilder('\Wave\Framework\Application\Url')
            ->enableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMock();

        $mockUrl->expects($this->any())
            ->method('getPath')
            ->willReturn('/');

        $mockRequest = $this->getMockBuilder('\Wave\Framework\Http\Request')
            ->disableOriginalConstructor()
            ->setMethods(['getMethod', 'getUrl'])
            ->getMock();

        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->willReturn('GET');

        $mockRequest->expects($this->any())
            ->method('getUrl')
            ->willReturnCallback(function () use ($mockUrl) {
                return $mockUrl;
            });

        $this->mockRouter = $this->getMockBuilder('\Wave\Framework\Application\Router')
            ->enableOriginalConstructor()
            ->setMethods(['dispatch', 'getPath'])
            ->getMock();
        $this->mockRouter->expects($this->any())
            ->method('dispatch')
            ->with('GET', '/')
            ->willReturnCallback(function () {
                echo 'Hello, World!';
            });

        $mockResponse = $this->getMockBuilder('\Wave\Framework\Http\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getHeaders'])
            ->getMock();

        $raw = [
            'HTTP_USER_AGENT' => 'PHP',
            'CONTENT_TYPE' => 'text/plain',
            'HTTP_COOKIE' => 'hello=world;'
        ];

        $this->server = new Server($mockRequest, $mockResponse, $raw);
    }
}
