<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 07/09/14
 * Time: 01:33
 */

namespace Tests\Http;


use Wave\Framework\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Wave\Framework\Http\Request
     */
    private $req;


    protected function setUp()
    {
        $_GET = array('foo' => 'bar');
        $_POST = array('bar' => 'baz');

        $this->req = new Request(array(
            'HTTP_USER_AGENT' => 'UnitTest/1.1',
            'REQUEST_URI' => '/',
            'REQUEST_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_PORT' => 80
        ));
    }

    public function testRequestAccess()
    {
        $this->assertSame('/', $this->req->uri());
        $this->assertSame('GET', $this->req->method());
        $this->assertSame('HTTP/1.1', $this->req->protocol());
        $this->assertSame(80, $this->req->request('Port'));
        $this->assertNull($this->req->request('Beer'));
    }

    public function testHeaderAccess()
    {
        $this->assertSame('UnitTest/1.1', $this->req->header('UserAgent'));
        $this->assertNull($this->req->header('Latte'));
        $this->assertSame(1, count($this->req->headers()));
        $this->assertSame(
            array('UserAgent' => 'UnitTest/1.1'),
            $this->req->headers()
        );
    }

    public function testParameterAccess()
    {
        $this->assertSame('bar', $this->req->param('foo'));
        $this->assertSame('baz', $this->req->param('bar'));
        $this->assertnull($this->req->param('baz'));
        $this->assertSame(
            array('foo' => 'bar', 'bar' => 'baz'),
            $this->req->params()
        );
    }
}
