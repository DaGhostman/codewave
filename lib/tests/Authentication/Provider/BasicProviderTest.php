<?php

use Wave\Authentication\Provider\BasicProvider;

class BasicProviderTest extends PHPUnit_Framework_TestCase
{
    private $adapter;
    private $container;
    private $provider;
    
    
    protected function setUp()
    {
        $this->adapter = $this->getMockBuilder(
	       '\Wave\Authentication\Adapter\DatabaseAdapter'
        )->disableOriginalConstructor()
        ->getMock();
        
        $this->container = $this->getMockbuilder(
	       '\Wave\Session\Container\BasicContainer'
        )->disableOriginalConstructor()
        ->getMock();
        
        $this->provider = new BasicProvider();
    }
    
    public function testAdapterInjection()
    {
        $ref = new \ReflectionObject($this->provider);
        $prop = $ref->getProperty('adapter');
        $prop->setAccessible(true);
        
        $this->provider->setAdapter($this->adapter);
        $this->assertSame($this->adapter, $prop->getValue($this->provider));
    }
    
    public function testContainerInjection()
    {
        $ref = new \ReflectionObject($this->provider);
        $prop = $ref->getProperty('container');
        $prop->setAccessible(true);
    
        $this->provider->setContainer($this->container);
        $this->assertSame($this->container, $prop->getValue($this->provider));
    }
    
    public function testTokensValidation()
    {
        $this->assertTrue($this->provider->validate('credential', 'secret', function($credential, $secret){
        	if ($credential === 'credential' && $secret === 'secret')
        	    return true;
        	
        	return false;
        }));
        
        function validMe($credential, $secret) {
            if ($credential === 'credential' && $secret === 'secret')
                return true;
             
            return false;
        }
        $this->assertTrue($this->provider->validate('credential', 'secret', 'validMe'));
        
        $this->assertNull($this->provider->validate('credential', 'secret', 'someFunctionthatDoesNotExist'));
    }
    
    public function testAuthenticationAndIdentity()
    {
        
        $adapter = $this->adapter;
        $adapter->expects($this->any())
            ->method('get')
            ->will($this->returnValue(array('id' => 1, 'user' => 'johndoe')));
        
        $container = $this->container;
        $container->expects($this->any())
            ->method('populate')
            ->will($this->returnValue(array('id' => 1, 'user' => 'johndoe')));
        
        $this->provider->setAdapter($adapter);
        $this->provider->setContainer($container);
        
        
        $this->assertTrue($this->provider->authenticate('credential', 'secret'));
    }
    
    public function testAuthenticationFailure()
    {
        $adapter = $this->adapter;
        $adapter->expects($this->any())
        ->method('get')
        ->will($this->returnValue(null));
        $this->provider->setAdapter($adapter);
        $this->assertFalse($this->provider->authenticate('credential', 'secret'));
    }
    
    public function testIdentityEmpty()
    {
        $this->assertNull($this->provider->getIndentity());
    }
}