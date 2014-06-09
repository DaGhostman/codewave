<?php

use \Wave\Authentication\Adapter\DatabaseAdapter;
use Wave\Database\Engine;

class StubEngine extends Engine
{
    public function __construct(){}
}

class DatabaseAdapterTest extends PHPUnit_Framework_TestCase
{
    private $db;
    
    protected function setUp()
    {
        $this->db = $this->getMockBuilder(
	       '\Wave\Database\Engine'
        )->disableOriginalConstructor()
        ->getMock();
    }
    
    public function testObjectCreation()
    {
        $adapter = new DatabaseAdapter('someTable', array('credential','secret'));
        $ref = new \ReflectionObject($adapter);
        $tableProperty = $ref->getProperty('table');
        $tableProperty->setAccessible(true);
        
        $this->assertEquals('someTable', $tableProperty->getValue($adapter));
    }
    
    public function testDatabaseInjection()
    {
        $adapter = new DatabaseAdapter('', array('',''));
        $this->assertSame($adapter, $adapter->setDatabase(new StubEngine(array())));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionDatabaseInjection()
    {
        $adapter = new DatabaseAdapter('', array('',''));
        $adapter->setDatabase(new \stdClass());
    }
    
    public function testGetMethod()
    {
        $db = $this->db;
        
        $db->expects($this->any())
            ->method('select')
            ->will($this->returnValue($db));
        
        $db->expects($this->any())
            ->method('where')
            ->will($this->returnValue($db));
        
        $db->expects($this->any())
            ->method('bind')
            ->will($this->returnValue($db));
        
        $db->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($db));
        
        $db->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue(
                new \ArrayObject(array('id' => 1, 'user' => 'johndoe'))
            ));
        
        $adapter = new DatabaseAdapter('someTable', array('credential','secret'));
        $ref = new \ReflectionObject($adapter);
        $dbProperty = $ref->getProperty('database');
        $dbProperty->setAccessible(true);
        $dbProperty->setValue($adapter, $db);
        
        $this->assertInstanceOf('\ArrayObject', $adapter->get('credential[:]secret'));
        
        
        
    }
}