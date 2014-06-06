<?php
use \Wave\Database\Adapter\PdoAdapter;

class PdoAdapterTest extends PHPUnit_Framework_TestCase
{

    protected $config = array(
        'driver' => 'PDO_DRIVER',
        'database' => 'PDO_DATABASE',
        'username' => 'PDO_USERNAME',
        'password' => 'PDO_PASSWORD',
        'port' => 'PDO_PORT'
    );

    protected $driver;

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
        
        $this->driver = new PdoAdapter($this->config);
    }

    public function testObjectInstantiation()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('Instance comparison in HHVM works differently');
        }
        $this->assertEquals($this->driver, new PdoAdapter($this->config));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConnectionSwitching()
    {
        $connect = $this->driver->connect();
        $this->assertEquals($this->driver, $connect);
        $this->assertInstanceOf('\Wave\Database\Adapter\PdoAdapter', $connect);
        $this->assertTrue($this->driver->connect());
        
        $disconnect = $this->driver->disconnect();
        $this->assertEquals($this->driver, $disconnect);
        $this->assertInstanceOf('\Wave\Database\Adapter\PdoAdapter', $disconnect);
        
        $this->driver->disconnect();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadPrepare()
    {
        $this->driver->prepare("SELECT fail FROM teapot");
        $this->driver->execute();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadDisconnect()
    {
        $drv = new PdoAdapter(array(
            'driver' => 'mysql',
            'database' => '',
            'hostname' => '',
            'username' => '',
            'password' => ''
        ));
        
        $drv->disconnect();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadConnect()
    {
        $drv = new PdoAdapter(array(
            'driver' => 'mysql',
            'database' => '',
            'hostname' => '',
            'username' => '',
            'password' => ''
        ));
        
        $drv->connect();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testbadDriver()
    {
        new PdoAdapter(array(
            'driver' => "noneExistingDriver"
        ));
    }

    public function testPrepareStatement()
    {
        $this->driver->prepare("CREATE TABLE IF NOT EXISTS table_name (id INTEGER NOT NULL, field TEXT NOT NULL);")->execute();
        $this->assertSame($this->driver, $this->driver->prepare("SELECT * FROM table_name"));
    }

    public function testOtherQueryStrings()
    {
        $connection = array(
            'username' => 'root',
            'password' => 'root',
            'database' => 'test',
            'hostname' => 'localhost'
        );
        $pgsql = new PdoAdapter(array_merge($connection, array(
            'driver' => 'pgsql'
        )));
        $mysql = new PdoAdapter(array_merge($connection, array(
            'driver' => 'mysql'
        )));
        
        $this->assertInstanceOf('\Wave\Database\Adapter\PdoAdapter', $pgsql);
        $this->assertInstanceOf('\Wave\Database\Adapter\PdoAdapter', $mysql);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExecuteNoPreparedStatementExceptions()
    {
        $this->driver->execute();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExecuteQueryException()
    {
        $drv = new PdoAdapter($this->config);
        $drv->prepare("CREATE TABLE IF NOT EXISTS table_name "
            ."(id INTEGER NOT NULL, field TEXT NOT NULL);")
            ->execute();
        $drv->prepare("SELECT * FROM :table WHERE id = :id AND field = :field)");
        $this->assertFalse($drv->execute(array("id" => 1, "field" => "hh")));
    }
    
    public function testFetchingResults()
    {   
        $drv = new PdoAdapter($this->config);
        $drv->prepare(
            "CREATE TABLE IF NOT EXISTS testTable (id INTEGER NOT NULL, asd TEXT NOT NULL);"
        )->execute();
        
        $drv->prepare("INSERT INTO testTable VALUES (1, :string);")
            ->execute(array("string" => "A"));
        
        $drv->prepare("SELECT * FROM testTable")
            ->execute();
        
        
        $this->assertSame(
            array('id' => '1', '1', 'asd' => 'A', 'A'),
            $drv->fetch()
        );
        
        $drv->prepare("SELECT * FROM testTable")
            ->execute();
        
        $this->assertSame(
            array(array('id' => '1', '1', 'asd' => 'A', 'A')),
            $drv->fetchAll()
        );
    }
    
    public function testLastInsertId()
    {
        $this->driver->prepare("CREATE TABLE smallTable (id INTEGER NOT NULL PRIMARY KEY, small TEXT);");
        $this->driver->execute();
        
        $this->driver->prepare("INSERT INTO smallTable (small) VALUES ('test')");
        $this->driver->execute();
        
        $this->assertSame('1', $this->driver->lastInsertId());
    }
    
    public function testGetQuery()
    {
        $this->driver->prepare("CREATE TABLE IF NOT EXISTS testTable (id INTEGER NOT NULL);");
        $this->assertSame("CREATE TABLE IF NOT EXISTS testTable (id INTEGER NOT NULL);", $this->driver->getQuery());
    }
    
    public function testParameterBind()
    {
        $this->driver->prepare("CREATE TABLE testTable (id INTEGER NOT NULL)")
            ->execute();
        $this->driver->prepare("SELECT * FROM testTable WHERE id = :id");
        $this->assertSame($this->driver, $this->driver->bindParam('id', 1, \PDO::PARAM_INT));
    }
}
