<?php
namespace Test\Http;

use Wave\Framework\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Response
     */
    protected $response;

    protected function setUp()
    {
        $this->response = new Response();
    }

    public function testHeaderGetters()
    {
        $response = new Response(['x-header' => 'value']);

        $this->assertTrue($response->hasHeader('x-header'));
        $this->assertSame('value', $response->getHeader('X-HEADER'));
    }

    public function testUndefinedHeaders()
    {
        $response = $this->response;

        $this->assertNull($response->getHeader('undefined-header'));
        $this->assertFalse($response->hasHeader('undefined-header'));
    }

    public function testArrayHeaderGetters()
    {
        $response = $this->response;

        $this->response->addHeader('x-header', ['value1', 'value2']);
        $this->assertSame(['value1', 'value2'], $response->getHeader('x-header'));
    }

    public function testHeaderSetters()
    {
        $response = $this->response;

        $response->addHeader('x-custom-header', 'value');
        $this->assertTrue($response->hasHeader('x-custom-header'));
        $this->assertSame('value', $response->getHeader('x-custom-header'));
    }

    public function testHeaderOverwrite()
    {
        $response = $this->response;

        $response->addHeader('x-header', 'value1');
        $response->addHeader('x-header', 'value2');
        $this->assertCount(2, $response->getHeader('x-header'));
        $response->addHeader('x-header', 'value', false);
        $this->assertSame('value', $response->getHeader('x-header'));
    }

    public function testMultiHeaderSetter()
    {
        $response = $this->response;

        $response->addHeaders(['x-header' => ['value1', 'value2'], 'x-header1' => 1]);
        $this->assertSame(['value1', 'value2'], $response->getHeader('x-header'));
        $this->assertSame(1, $response->getHeader('x-header1'));
    }

    public function testStatusCodeException()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $this->response->setStatus(123);
    }

    public function testStatusCode()
    {
        $response = $this->response->setStatus(404);
        $this->assertSame([404, 'Not Found'], $response->getStatus());
    }

    public function testVersion()
    {
        $this->assertSame(1.0, $this->response->getVersion());
        $this->response->setVersion(1.1);
        $this->assertSame(1.1, $this->response->getVersion());
    }

    public function testInvalidHttpVersionArgumentTypeException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->response->setVersion(3);
    }

    public function testListOfHeaders()
    {
        $this->response->addHeader('x-header', 'value');
        $this->assertSame(['X-Header' => ['value']], $this->response->getHeaders());
    }

    public function testBodyGetterAndSetter()
    {
        $this->assertSame('', $this->response->getBody());
        $this->assertNull($this->response->setBody('content'));
        $this->assertSame('content', $this->response->getBody());
    }
}
