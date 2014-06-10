<?php

use Wave\Application\Environment;
class EnvironmentTest extends PHPUnit_Framework_TestCase
{
    private $stubEnv = null;
    protected function setUp()
    {
        $this->stubEnv = new Environment();
    }
    
    public function testIssetOnConstructBootstrap()
    {
        $env = new Environment(array(
        	'some_value' => true,
            'with.point' => true,
            'some very long key' => true
        ));
        
        $this->assertTrue(isset($env->some_value));
        $this->assertTrue(isset($env['some_value']));
        $this->assertTrue(isset($env['some very long key']));
        $this->assertTrue(isset($env['with.point']));
    }
    
    public function testIssetOnTheGo()
    {
        $this->stubEnv->key = true;
        $this->stubEnv['array_key'] = true;
        
        $this->assertTrue(isset($this->stubEnv->key));
        $this->assertTrue(isset($this->stubEnv['array_key']));
        $this->assertTrue(isset($this->stubEnv->array_key));
        $this->AssertTrue(isset($this->stubEnv['key']));
        
        $this->assertTrue($this->stubEnv->key);
        $this->assertTrue($this->stubEnv['key']);
        
        $this->assertTrue($this->stubEnv->array_key);
        $this->assertTrue($this->stubEnv['array_key']);
    }
    
    public function testUnset()
    {
        $env = $this->stubEnv;
        
        unset($env->some_value);
        $this->assertFalse(isset($env->some_value));
        
        unset($env['some very long key']);
        $this->assertFalse(isset($env['some very long key']));
    }
    
    public function testSerialization()
    {
        $env = new Environment(array('key' => 'value', 1, 'kkey' => 2));
        
        $this->assertSame(serialize(new Environment(array('key' => 'value', 1, 'kkey' => 2))), serialize($env));
    }
    
    public function testUnserialization()
    {
        $env = serialize($this->stubEnv);
        
        $this->assertEquals($this->stubEnv, unserialize($env));
        
        
    }
}