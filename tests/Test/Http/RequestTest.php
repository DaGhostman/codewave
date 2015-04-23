<?php

namespace Test\Http;

use Wave\Framework\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected $server = [];
    protected $url;
    protected $request;

    protected function setUp()
    {
        parent::setUp();
        $this->server = [
            'method' => 'GET',
            'url' => '/',
            'HTTP_USER_AGENT' => 'UnitTest',
            'x-legit-header' => 'OK'
        ];

        $this->url = $this->getMockBuilder('\Wave\Framework\Http\Url')
            ->disableOriginalConstructor()
            ->setMethods(['getMethod', 'getPath'])
            ->getMock();

        $this->request = (new Request($this->server['method'], $this->url))
            ->addHeaders($this->server);
    }

//    public function testImmutability()
//    {
//        $hash = $request = new Request('GET', $this->url);
//        $request->addHeader('Test', '55');
//        $this->assertSame($hash, $request);
//
//        //var_dump($r);
//    }

    public function testMethodExistence()
    {
        $this->assertTrue(method_exists($this->request, 'getHeader'));
    }

    public function testHeaderGetters()
    {
        $this->assertSame('UnitTest', $this->request->getHeader('HTTP_USER_AGENT'));
        $this->assertSame('OK', $this->request->getHeader('x-legit-header'));
    }

    public function testHeadersExistence()
    {
        $this->assertNotSame($this->server, $this->request->getHeaders());
        $this->assertSame($this->server['HTTP_USER_AGENT'], $this->request->getHeader('HTTP_USER_AGENT'));
        $this->assertSame($this->server['x-legit-header'], $this->request->getHeader('x-legit-header'));
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->server = null;
        $this->request = null;
        $this->url = null;
    }
}
