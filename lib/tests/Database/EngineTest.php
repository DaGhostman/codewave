<?php
use \Wave\Database\Engine;

class EngineTest extends PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected $config = array(
        'driver' => 'PDO_DRIVER',
        'database' => 'PDO_DATABASE',
        'username' => 'PDO_USERNAME',
        'password' => 'PDO_PASSWORD',
        'port' => 'PDO_PORT'
    );
    
    protected function setUp()
    {
        if (! extension_loaded('pdo')) {
            $this->markTestSkipped('PDO Extension is unavailable');
        }
        
        foreach ($this->config as $key => $value) {
            
            if (! isset($GLOBALS[$value])) {
                $this->markTestSkipped('Unable to find variable: ' . $value);
            }
            
            $this->config[$key] = $GLOBALS[$value];
        }
        
        $this->adapter = new \Wave\Database\Adapter\PdoAdapter($this->config);
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
            
        $engine = new Engine($adapter);
        $this->assertSame($adapter, $engine->getLink());
    }
    
    public function testResultFetching()
    {
        $adapter = $this->getMockBuilder('\Wave\Database\Adapter\PdoAdapter')
            ->disableOriginalConstructor()
            ->getMock();
        
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
            
        $engine = new Engine($adapter);
        
        $this->assertEquals(new \ArrayObject(array('id' => 1, 1, 'field' => 'test', 'test')), $engine->fetch());
        $this->assertEquals(new \ArrayObject(array(array('id' => 1, 1, 'field' => 'test', 'test'))), $engine->fetch(null, true));
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testResultException()
    {
        $adapter = $this->adapter;
        
        
        $engine = new Engine($adapter);
        $engine->fetch();
    }
    
    public function testHandlerChangeInFetch()
    {
        $adapter = $this->getMockBuilder('\Wave\Database\Adapter\PdoAdapter')
            ->disableOriginalConstructor()
            ->getMock();
        
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
        $adapter->prepare("CREATE TABLE IF NOT EXISTS dummyTable (id INTEGER PRIMARY KEY, name TEXT);")
            ->execute();
        
        /**
         * Test Engine::insert()
         */
        $engine = new Engine($adapter);
        $this->assertInstanceOf('\Wave\Database\Engine', ($ins = $engine->insert('dummyTable', array('id', 'name'))));
        $ins->execute(array('id' => 5, 'name' => 'Joe'));
        $this->assertEquals(5, $ins->getLink()->lastInsertId());
        
        /**
         * Test Engine::select()
         */
        $this->assertInstanceOf('\Wave\Database\Engine', ($sel = $engine->select('dummyTable', array('id', 'name'))));
        $this->assertEquals(
            new \ArrayObject(array('id' => 5, 5, 'name' => 'Joe', 'Joe')),
            $sel->execute()->fetch()
        );
        
        /**
         * Test Engine::update()
         */
        $this->assertInstanceOf('\Wave\Database\Engine', ($upd = $engine->update('dummyTable', array('name'))));
        $this->assertInstanceOf('\Wave\Database\Engine', $upd->where("id = :id"));
        
        $upd->execute(array('id' => 5, 'name' => 'Mike'));
        $this->assertEquals(5, $upd->getLink()->lastInsertId());
        
        /**
         * Testing Engine::custom()
         */
        $select = $engine->select('dummyTable', array('name'));
        $this->assertInstanceOf('\Wave\Database\Engine', $select->custom("WHERE id = :id ORDER BY id"));
        $res = $select->execute(array('id' => 5))->fetch();
        $this->assertEquals('Mike', $res['name']);
        
        /**
         * Testing Engine::delete()
         */
        $this->assertInstanceOf('\Wave\Database\Engine', ($del = $engine->delete('dummyTable')));
        $del->where('id = :id')->execute(array('id' => 5));
        $this->assertEquals(5, $del->getLink()->lastInsertId());
        
        
    }
    
    public function testBindParams()
    {
        $adapter = $this->getMockBuilder('\Wave\Database\Adapter\PdoAdapter')
            ->disableOriginalConstructor()
            ->getMock();
        
        $adapter->expects($this->any())
            ->method('bindParam')
            ->will($this->returnValue(null));
        
        $engine = new Engine($adapter);
        $this->assertInstanceOf('\Wave\Database\Engine',$engine->bind('id', 1, \PDO::PARAM_INT));
    }
    
    public function testExecute()
    {
        $adapter = $this->getMockBuilder('\Wave\Database\Adapter\PdoAdapter')
            ->disableOriginalConstructor()
            ->getMock();
    
        $adapter->expects($this->any())
        ->method('execute')
        ->will($this->returnValue(true));
    
        $engine = new Engine($adapter);
        $this->assertInstanceOf('\Wave\Database\Engine', $engine->execute(array()));
    }
    
    public function testHandlerMethod()
    {
        $adapter = $this->getMockBuilder('\Wave\Database\Adapter\PdoAdapter')
        ->disableOriginalConstructor()
        ->getMock();
        $engine = new Engine($adapter);
        
        $this->assertEquals(new \ArrayObject(array(5)), $engine->handler(5));
    }
    
    /**
     * @expectedException \ErrorException
     */
    public function testExecuteException()
    {
        $adapter = $this->getMockBuilder('\Wave\Database\Adapter\PdoAdapter')
            ->disableOriginalConstructor()
            ->getMock();
        
        $engine = new Engine($adapter);
        $engine->execute();
    
        $this->assertEquals(new \ArrayObject(array(5)), $engine->handler(5));
    }
    
    
}