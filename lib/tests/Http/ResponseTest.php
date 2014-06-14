<?php

use Wave\Http\Response;
class ResponseTest extends PHPUnit_Framework_TestCase
{
    protected $reflect = null;
    protected $response = null;
    
    protected function setUp()
    {
        $this->response = new Response();
        
        $reflect = new \ReflectionObject($this->response);
        $prop = $reflect->getProperty('headers');
        $prop->setAccessible(true);
        
        $this->reflect = $prop;
        
    }
    
    public function testConstructionProtocol()
    {
        $r = new Response('HTTP/1.1');
        $ref = new \ReflectionObject($r);
        $prop = $ref->getProperty('protocol');
        $prop->setAccessible(true);
        
        $this->assertSame('HTTP/1.1', $prop->getValue($r));
    }
    
    public function testRedirect301()
    {
        $r = $this->response->redirect('/', true);
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        
        $this->assertSame(array(301 => 'Location: /'), $this->reflect->getValue($r));
        
    }
    
    public function testRedirect302()
    {
        $r = $this->response->redirect('/');
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        
        $this->assertSame(array(302 => 'Location: /'), $this->reflect->getValue($r));
    }
    
    public function testHaltStatusCode()
    {
        $r = $this->response->halt();
        
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
            array(500 => 'HTTP/1.1 500 Internal Error'),
            $this->reflect->getValue($r)
        );
    }
    
    public function testOK()
    {
        $r = $this->response->OK();
        
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
        	array(200 => 'HTTP/1.1 200 OK'),
            $this->reflect->getValue($r)
        );
    }
    
    public function testForbiden()
    {
        $r = $this->response->forbidden();
    
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
            array(403 => 'HTTP/1.1 403 Forbidden'),
            $this->reflect->getValue($r)
        );
    }
    
    public function testNotFound()
    {
        $r = $this->response->notFound();
        
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
        	array(404 => 'HTTP/1.1 404 Not Found'),
            $this->reflect->getValue($r)
        );
    }
    
    public function testUnauthorized()
    {
        $r = $this->response->unauthorized();
    
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
            array(401 => 'HTTP/1.1 401 Unauthorized'),
            $this->reflect->getValue($r)
        );
    }
    
    /**
     * @covers \Wave\Http\Response::send
     */
    public function testHeaderSending()
    {
        $this->response->OK()->send();
        
        if (defined('HHVM_VERSION')) {
            /**
             * @TODO: Look deeper in to the issue
             */
            $this->markTestSkipped('HHVM Does not return false, says its null(!?!).');
        }
        
        /*
         * This is expected as command line is headerless context
         */
        $this->assertFalse($this->response->send());
    }
}