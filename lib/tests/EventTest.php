<?php
use \Wave\Event;
class EventTest extends PHPUnit_Framework_TestCase 
{
        
    protected function setUp() {
        $this->stance = new \stdClass();
        $this->env = new \stdClass();
        
        $this->event = new Event;
    }
    
    public function testInstance() {
        $this->assertEquals($this->event, new Event);
    }
    
    public function testSetters() {
        
        $this->assertInstanceOf('\Wave\Event', $this->event->setStance($this->stance));
        $this->assertInstanceOf('\Wave\Event', $this->event->setEnv($this->env));
        
    }
    
    public function testGetters() {
        $this->assertInstanceOf('\Wave\Event', $this->event->setStance($this->stance));
        $this->assertInstanceOf('\Wave\Event', $this->event->setEnv($this->env));
        
        
        $this->assertEquals($this->event->getStance(), $this->stance);
        $this->assertEquals($this->event->getEnv(), $this->env);
        
    }
    
    public function testIsset() {
        $this->assertInstanceOf('\Wave\Event', $this->event->setStance($this->stance));
        $this->assertInstanceOf('\Wave\Event', $this->event->setEnv($this->env));
        
        $this->assertTrue(isset($this->event->env));
        $this->assertTrue(isset($this->event->stance));
    }

    public function testName() {
        $this->event->setname("Hello");
        
        $this->assertEquals("Hello", $this->event->getName());
    }
}
?>