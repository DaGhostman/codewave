<?php

namespace Test\Http;

use Wave\Framework\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected $server = [];
    protected $url;
    /**
     * @type \Wave\Framework\Interfaces\Http\RequestInterface
     */
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

        $this->request = new Request($this->server['method'], $this->url, $this->server);
    }

    public function testBadHttpMethod()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new Request('READ', $this->url);
    }

    public function testHeaderGetters()
    {
        $this->assertSame('UnitTest', $this->request->getHeader('HTTP_USER_AGENT'));
        $this->assertSame('OK', $this->request->getHeader('x-legit-header'));
        $this->assertNull($this->request->getHeader('non-existing-header'));
    }

    public function testHeadersExistence()
    {
        $this->assertNotSame($this->server, $this->request->getHeaders());
        $this->assertSame($this->server['HTTP_USER_AGENT'], $this->request->getHeader('HTTP_USER_AGENT'));
        $this->assertSame($this->server['x-legit-header'], $this->request->getHeader('x-legit-header'));
    }

    public function testConstructorHeaders()
    {
        $req = new Request('GET', $this->url, [
            'User-Agent' => 'UnitTest',
            'x-legit-header' => 'OK',
            'content-type' => 'text/plain'
        ]);

        $this->assertTrue($req->hasHeader('USER-AGENT'));
        $this->assertTrue($req->hasHeader('X-LEGIT-HEADER'));
        $this->assertTrue($req->hasHeader('CONTENT-TYPE'));

        $this->assertSame('UnitTest', $req->getHeader('user-agent'));
        $this->assertSame('OK', $req->getHeader('x-legit-header'));
        $this->assertSame('text/plain', $req->getHeader('content-type'));
    }

    public function testImmutability()
    {
        $hash = $request = new Request('GET', $this->url);
        $request = $request->addHeader('Test', '55');
        $this->assertNotSame($hash, $request);


    }

    public function testHeaderAppending()
    {
        $request = $this->request->addHeader('x-legit-header', 'PERFECT');
        $this->assertSame(['OK', 'PERFECT'], $request->getHeader('x-legit-header'));
    }
    public function testSetterOverwriting()

    {
        $request = $this->request->addHeader('X-Legit-Header', 'PERFECT', false);
        $this->assertSame('PERFECT', $request->getHeader('x-legit-header'));
    }

    public function testMultiHeaderAppending()
    {
        $request = new Request('get', $this->url, []);
        $request = $request->addHeaders([
            'x-legit-headers' => ['PERFECTLY', 'WORKING'],
            'x-legit-header' => '!!!'
        ]);

        $this->assertEquals(
            ['X-Legit-Header' =>
                 ['!!!'],
             'X-Legit-Headers' =>
                 ['PERFECTLY', 'WORKING']
            ],
            $request->getHeaders()
        );
    }

    public function testAdditionalGetters()
    {
        $this->assertEmpty($this->request->getBody());
        $this->assertSame($this->url, $this->request->getUrl());
        $this->assertSame('GET', $this->request->getMethod());
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->server = null;
        $this->request = null;
        $this->url = null;
    }
}