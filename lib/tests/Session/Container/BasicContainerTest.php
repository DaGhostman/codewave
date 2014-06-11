<?php

use Wave\Pattern\Observer\Subject;
use Wave\Session\Container\BasicContainer;
use Wave\Session\Adapter\AbstractAdapter;


class StubSubject extends Subject {}
class StubAdapter extends AbstractAdapter
{
    public function __construct($source){}
    public function update($data){}
    public function fetch()
    {
        return array('k' => 'val', 2);
    }
}

class BasicContainerTest extends PHPUnit_Framework_TestCase
{
    protected $container = null;
    
    protected function setUp()
    {
        $this->container = new BasicContainer(new StubSubject());
    }
    
    
    public function testContainerPopulation()
    {
        $this->assertInstanceOf(
            '\Wave\Session\Container\BasicContainer',
            $this->container->populate(array('key' => true))
        );
    }
    
    public function testAdapterAddition()
    {
        $adapter = new StubAdapter(null);
        
        $this->container->setAdapter($adapter);
        
        $this->assertSame($adapter, $this->container->getAdapter());
        
    }
    
    public function testGetters()
    {
        $this->container->populate(array('key' => 'value'));
        
        $this->assertSame('value', $this->container->key);
        $this->assertSame('value', $this->container['key']);
    }
    
    public function testSettersAndIsset()
    {
        $adapter = new StubAdapter(null);
        
        $this->container->setAdapter($adapter);
        
        $this->container->key = 'value';
        $this->assertTrue(isset($this->container->key));
        $this->assertTrue(isset($this->container['key']));
        $this->assertSame('value', $this->container->key);
        $this->assertSame('value', $this->container['key']);
        
        
        $this->container['key2'] = 'value2';
        $this->assertTrue(isset($this->container->key2));
        $this->assertTrue(isset($this->container['key2']));
        $this->assertSame('value2', $this->container->key2);
        $this->assertSame('value2', $this->container['key2']);
    }
    
    public function testUnset()
    {
        $adapter = new StubAdapter(null);
        
        $this->container->setAdapter($adapter);
        unset($this->container->k);
        $this->assertFalse(isset($this->container->k));
        
        unset($this->container[0]);
        $this->assertFalse(isset($this->container[0]));
    }
}