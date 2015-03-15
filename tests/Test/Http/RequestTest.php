<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 01/02/15
 * Time: 16:17
 */
namespace Test\Http;

use Wave\Framework\Http\Server\Request;

/**
 * @noinspection PhpUndefinedClassInspection
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{

    private $request = null;

    protected function setUp()
    {

        $serverDummy = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'HTTP_USER_AGENT' => 'Mozilla 5.0',
            'REQUEST_PROTOCOL' => 'HTTP/1.1',
            'HTTP_X_CUSTOM_H' => 'Dummy-Test-Data-2.0',
            'HTTP_ACCEPT_ENCODING' => 'application/json'
        ];

        $this->request = new Request('/', 'GET', 'php://memory', [
            'User-Agent' => 'Mozilla 5.0',
            'X-Custom-H' => 'Dummy-Test-Data-2.0',
            'Accept-Encoding' => 'application/json'
        ]);
    }

    public function testRequestGetter()
    {
        $this->assertSame('/', $this->request->getUri()->getPath());

        $this->assertSame('GET', $this->request->getMethod());

        $this->assertSame('1.1', $this->request->getProtocolVersion());
    }

    public function testHeaderGetters()
    {
        $this->assertSame('Mozilla 5.0', $this->request->getHeader('User-Agent'));
        $this->assertSame('Dummy-Test-Data-2.0', $this->request->getHeader('X-Custom-H'));
        $this->assertSame('application/json', $this->request->getHeader('Accept-Encoding'));
    }

    public function testRawHeaders()
    {
        $this->assertSame([
            'user-agent' => ['Mozilla 5.0'],
            'x-custom-h' => ['Dummy-Test-Data-2.0'],
            'accept-encoding' => ['application/json']
        ], $this->request->getHeaders());
    }

    public function testNotExistingVariables()
    {
        $this->assertEmpty($this->request->getHeader('SomeDummyHeader'));
    }
}
