<?php
use \Wave\Database\Engine;

class EngineTest extends PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected function setUp()
    {
        $this->adapter = $this->getMockBuilder(
            '\Wave\Database\Adapter\PdoAdapter'
        )->disableOriginalConstructor()
        ->getMock();
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadConstructor()
    {
        new Engine(new \stdClass());
    }
    
    public function testObjectCreation()
    {
        $adapter = $this->adapter; 
        
        $this->assertInstanceOf('\Wave\Database\Engine', new Engine($this->adapter));
    }
    
    public function testgetLink()
    {
        $adapter = $this->adapter;
        $adapter->expects($this->any())
            ->method('getLink')
            ->will($this->returnValue(new \stdClass()));
            
        $engine = new Engine($adapter);
        $this->assertSame($adapter, $engine->getLink());
    }
    
    public function testResultFetching()
    {
        $adapter = $this->adapter;
        $adapter->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue(
                array('id' => 1, 1, 'field' => 'test', 'test')
            )
        );
        
        $adapter->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue(
        	   array(array('id' => 1, 1, 'field' => 'test', 'test'))
            )
        );
            
        $engine = new Engine($this->adapter);
        
        $this->assertEquals(new \ArrayObject(array('id' => 1, 1, 'field' => 'test', 'test')), $engine->fetch());
        $this->assertEquals(new \ArrayObject(array(array('id' => 1, 1, 'field' => 'test', 'test'))), $engine->fetch(null, true));
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testResultException()
    {
        $adapter = $this->adapter;
        
        $adapter->expects($this->any())
            ->method('fetch')
            ->will($this->throwException(new \RuntimeException("Test Success", -1)));
        
        $engine = new Engine($adapter);
        $engine->fetch();
    }
    
    public function testHandlerChangeInFetch()
    {
        $adapter = $this->adapter;
        
        $adapter->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue(array()));
        
        $engine = new Engine($adapter);
        $result = $engine->fetch('\stdClass');
        
        $this->assertInstanceOf('\stdClass', $result);
    }
    
    public function testHandlerCreation()
    {
        $engine = new Engine($this->adapter);
        $engine->setResulthandler('\ArrayObject');
        $result = $engine->handler(array(1, 2, 3, 4));
        
        $this->assertInstanceOf('\ArrayObject', $result);
        $this->assertEquals(new \ArrayObject(array(1, 2, 3, 4)), $result);
    }
    
    public function testCRUDMethods()
    {
        $adapter = $this->adapter;
        $adapter->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue(true));
        
        $adapter->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue('SELECT * FROM dummyTable'));
        
        $engine = new Engine($adapter);
        $this->assertInstanceOf('\Wave\Database\Engine', $engine->insert('dummyTable', array('id', 'name')));
        $this->assertInstanceOf('\Wave\Database\Engine', $engine->select('dummyTable', array('id', 'name')));
        $this->assertInstanceOf('\Wave\Database\Engine', $engine->delete('dummyTable'));
        $this->assertInstanceOf('\Wave\Database\Engine', $engine->update('dummyTable', array('id', 'name')));
        $this->assertInstanceOf('\Wave\Database\Engine', $engine->where("column = :col"));
        $this->assertInstanceOf('\Wave\Database\Engine', $engine->custom("WHERE column = :col ORDER BY id"));
        
    }
    
    public function testBindParams()
    {
        $adapter = $this->adapter;
        
        $adapter->expects($this->any())
            ->method('bindParam')
            ->will($this->returnValue(null));
        
        $engine = new Engine($adapter);
        $this->assertInstanceOf('\Wave\Database\Engine',$engine->bind('id', 1, \PDO::PARAM_INT));
    }
    
    public function testExecute()
    {
        $adapter = $this->adapter;
    
        $adapter->expects($this->any())
        ->method('execute')
        ->will($this->returnValue(null));
    
        $engine = new Engine($adapter);
        $this->assertInstanceOf('\Wave\Database\Engine',$engine->execute(array()));
    }
    
    
}