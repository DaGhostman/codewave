<?php

namespace Test\Http;


use Stub\MockResponse;
use Wave\Framework\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    private $response, $mockInstance;

    protected function setUp()
    {
        $mockLink = $this->getMockBuilder('\Wave\Framework\Common\Link')
            ->setMethods(['notify'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockLink->expects(self::any())
            ->method('notify')
            ->willReturn(null);

        $this->mockInstance = $mockInstance = $this->getMockBuilder('\Stub\MockResponse')
            ->setMethods(['badMethod', 'getProtocolVersion', 'withAddedHeader'])
            ->getMock();

        $this->mockInstance
            ->expects(self::any())
            ->method('getProtocolVersion')
            ->willReturn('1.1');

        $this->mockInstance
            ->expects(self::any())
            ->method('withAddedHeader')
            ->willReturn($this->mockInstance);



        $this->response = new Response($mockInstance);
        $this->response->addLink($mockLink);
    }

    public function testReturnsDifferentValuedObjects()
    {
        self::assertNotSame(
            $this->response->getState(),
            $this->response->getState()
        );
    }

    public function testThrowsExceptionOnInvalidResponse()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Expected ResponseInterface, received \'stdClass\''
        );
        new Response(new \stdClass());
    }

    public function testResponseExceptionBadMethod()
    {
        $this->mockInstance->expects(self::once())
            ->method('badMethod')
            ->willThrowException(new \BadMethodCallException());

        $this->setExpectedException('\BadMethodCallException');
        $this->response->badMethod();
    }

    public function testProxyMethodCall()
    {
        self::setExpectedException('\InvalidArgumentException');
        self::assertSame('1.1', $this->response->getProtocolVersion());
        self::assertInstanceOf('\Stub\MockResponse', $this->response->withAddedHeader('test', 'double'));
    }
}
