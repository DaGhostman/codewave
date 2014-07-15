<?php

use Wave\Http\Request;
use Wave\Application\Environment;
class RequestTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $_SERVER = array(
        	'HTTP_ACCEPT_ENCODING' => 'text/html',
            'X_REQUESTED_WITH' => 'XMLHttpRequest'
        );
        $_REQUEST = array();
    }
    
    public function testClassInstantiation()
    {
        $req = new Request(new \Wave\Storage\Registry());
        $this->assertInstanceOf('\Wave\Http\Request', $req);
    }
    
    public function testParameterSetting()
    {
        $req = new Request(new \Wave\Storage\Registry());
        $req->setParams(array('param1' => 1, 'param2' => 'value'));
        
        $this->assertSame(1, $req->param('param1'));
        $this->assertSame('value', $req->param('param2'));
        $this->assertNull($req->param('non existing key'));
    }
    
    public function testRequestTypeCheck()
    {
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'GET'))));
        $this->assertTrue($req->isGet());
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'POST'))));
        $this->assertTrue($req->isPost());
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'PUT'))));
        $this->assertTrue($req->isPut());
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'DELETE'))));
        $this->assertTrue($req->isDelete());
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'OPTIONS'))));
        $this->assertTrue($req->isOptions());
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'TRACE'))));
        $this->assertTrue($req->isTrace());
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'CONNECT'))));
        $this->assertTrue($req->isConnect());
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'HEAD'))));
        $this->assertTrue($req->isHead());
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'DELETE'))));
        $this->assertTrue($req->isDelete());
        
        $this->assertTrue($req->isAjax());
        
        $_SERVER['X_REQUESTED_WITH'] = 'Ugly Service';
        $req = new Request(new \Wave\Storage\Registry());
        $this->assertFalse($req->isAjax());
    }
    
    public function testMethodGetter()
    {
        
        $req = new Request(new \Wave\Storage\Registry(array('data' => array('request.method' => 'GET'))));
        $this->assertSame('GET', $req->getMethod());
    }
    
    public function testHeadersGetter()
    {
        $req = new Request(new \Wave\Storage\Registry());
        $this->assertSame('text/html', $req->getHeader('AcceptEncoding'));
        $this->assertNull($req->getHeader('HeaderDontExist'));
    }
}