<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 01/02/15
 * Time: 16:17
 */
namespace Test\Http;

use Wave\Framework\Http\Request;

/**
 * @noinspection PhpUndefinedClassInspection
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{

    private $request = null;

    protected function setUp()
    {
        $requestData = [
            'param1' => 1,
            'param2' => 'dummy_string'
        ];
        
        $serverDummy = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'HTTP_USER_AGENT' => 'Mozilla 5.0',
            'REQUEST_PROTOCOL' => 'HTTP/1.1',
            'HTTP_X_CUSTOM_H' => 'Dummy-Test-Data-2.0',
            'HTTP_ACCEPT_ENCODING' => 'application/json'
        ];
        
        $this->request = new Request($serverDummy, $requestData);
    }

    public function testRequestGetter()
    {
        $this->assertSame('/', $this->request->uri());
        $this->assertSame('/', $this->request->REQUEST_URI);
        
        $this->assertSame('GET', $this->request->method());
        $this->assertSame('GET', $this->request->REQUEST_METHOD);
        
        $this->assertSame('HTTP/1.1', $this->request->REQUEST_PROTOCOL);
    }

    public function testHeaderGetters()
    {
        $this->assertSame('Mozilla 5.0', $this->request->header('UserAgent'));
        $this->assertSame('Dummy-Test-Data-2.0', $this->request->header('XCustomH'));
        $this->assertSame('application/json', $this->request->header('AcceptEncoding'));
    }

    public function testRawHeaders()
    {
        $this->assertSame([
            'UserAgent' => 'Mozilla 5.0',
            'XCustomH' => 'Dummy-Test-Data-2.0',
            'AcceptEncoding' => 'application/json'
        ], $this->request->headers());
    }

    public function testParamGetters()
    {
        $this->assertSame(1, $this->request->param('param1'));
        $this->assertSame('dummy_string', $this->request->param('param2'));
        
        $this->assertSame([
            'param1' => 1,
            'param2' => 'dummy_string'
        ], $this->request->params());
    }

    public function testToArray()
    {
        $stub = [
            "headers" => [
                "UserAgent" => 'Mozilla 5.0',
                "XCustomH" => 'Dummy-Test-Data-2.0',
                "AcceptEncoding" => 'application/json'
            ],
            "request" => [
                "Method" => 'GET',
                "Uri" => '/',
                "Protocol" => 'HTTP/1.1'
            ],
            "params" => [
                'param1' => 1,
                'param2' => 'dummy_string'
            ]
        ];
        
        $this->assertSame($stub, $this->request->toArray());
    }

    public function testNotExistingVariables()
    {
        $this->assertNull($this->request->header('SomeDummyHeader'));
        $this->assertNull($this->request->request('SomeDummyRequest'));
        $this->assertNull($this->request->DUMMY_HEADER);
    }
}
