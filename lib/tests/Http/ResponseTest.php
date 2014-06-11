<?php

use Wave\Http\Response;
class ResponseTest extends PHPUnit_Framework_TestCase
{
    protected $reflect = null;
    protected $response = null;
    
    protected function setUp()
    {
        $this->response = new Response();
        
        $reflect = new ReflectionObject($this->response);
        $prop = $reflect->getProperty('headers');
        $prop->setAccessible(true);
        
        $this->reflect = $prop;
        
    }
    
    public function testRedirects()
    {
        $r = $this->response->redirect('/');
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        
        $this->assertSame(array('Location: /' => 302), $this->reflect->getValue($r));
        
        $rr = $this->response->redirect('/', true);
        $this->assertInstanceOf('\Wave\Http\Response', $rr);
        
        $this->assertSame(array('Location: /' => 301), $this->reflect->getValue($rr));
        
    }
    
    public function testHaltStatusCode()
    {
        $r = $this->response->halt();
        
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
            array('500 Internal Error' => 500),
            $this->reflect->getValue($r)
        );
    }
    
    public function testOK()
    {
        $r = $this->response->OK();
        
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
        	array('200 OK' => 200),
            $this->reflect->getValue($r)
        );
    }
    
    public function testForbiden()
    {
        $r = $this->response->forbidden();
    
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
            array('403 Forbidden' => 403),
            $this->reflect->getValue($r)
        );
    }
    
    public function testNotFound()
    {
        $r = $this->response->notFound();
        
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
        	array('404 Not Found' => 404),
            $this->reflect->getValue($r)
        );
    }
    
    public function testUnauthorized()
    {
        $r = $this->response->unauthorized();
    
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
            array('401 Unauthorized' => 401),
            $this->reflect->getValue($r)
        );
    }
    
    /**
     * @covers \Wave\Http\Response::send
     */
    public function testHeaderSending()
    {
        $r = $this->response->OK();
    
        $this->assertInstanceOf('\Wave\Http\Response', $r);
        $this->assertSame(
            array('200 OK' => 200),
            $this->reflect->getValue($r)
        );
        
        /*
         * This is expected as command line is headerless context
         */
        $this->assertFalse($this->response->send(true));
    }
}