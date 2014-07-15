<?php
namespace Wave\Database\Adapter;

use \Wave\Database\Adapter\AbstractAdapter;

class PdoAdapter extends AbstractAdapter
{

    const ADAPTER_MYSQL = 'mysql';

    const ADAPTER_PGSQL = 'pgsql';

    const ADAPTER_SQLITE = 'sqlite';

    protected $dsn = null;

    protected $link = null;

    protected $stmt = null;

    public $username = null;

    public $password = null;

    /**
     * Constructs the DNS string required for connecting to the database
     * it does not perform the actual connection, but rather wiats for a
     * request to be made towards the database and initiates a the
     * connection.
     * Note that this proces only happens once per object
     * instantiation.
     *
     * @access public
     *        
     * @param array $config
     *            Assoc array with settings for the connection
     *            'hostname' Host of the database, filename for SQLite or socket
     *            'database' Database name, if using RDBMS
     *            'username' (Optional) Username
     *            'password' (Optional) Password
     *            'port' (Optional) Port to use when connecting
     *            
     * @param array $options
     *            Array with valid PDO options
     *            
     * @throws \InvalidArgumentException
     *
     */
    public function __construct($config, $options = array())
    {
        foreach ($config as $key => $value) {
            $$key = $value;
        }
        if ($driver == self::ADAPTER_MYSQL) {
            $this->dsn = sprintf("mysql:host=%s;dbname=%s;port=%s", $hostname, $database, (isset($port)? $port : 3306));
        } elseif ($driver == self::ADAPTER_PGSQL) {
            $this->dsn = sprintf("pgsql:host=%s;dbname=%s;port=%s", $hostname, $database, (isset($port)? $port : 3306));
        } elseif ($driver == self::ADAPTER_SQLITE) {
            $this->dsn = sprintf("sqlite:%s", $database);
        } else {
            throw new \InvalidArgumentException("Unknown adapter specified");
        }
        
        $this->password = (isset($password) ? $password : $this->password);
        $this->username = (isset($username) ? $username : $this->username);
    }

    /**
     * Checks if a connecting is established, if not it establishes it,
     * otherwise it returns.
     *
     * @access public
     * @param array $options Options to pass to PDO on instantiation
     * @return boolean True if connected, false otherwise
     * @throws \RuntimeException If unable to connect
     *        
     */
    public function connect($options = array())
    {
        if ($this->link && $this->link instanceof \PDO) {
            return true;
        }
        
        try {
            $this->link = new \PDO($this->dsn, $this->username, $this->password, $options);
            
            $this->link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $ex) {
            throw new \RuntimeException("Unable to instantiate PDO connection", $ex->getCode(), $ex);
        }
        
        return $this;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Wave\Database\Adapter\AbstractAdapter::disconnect()
     */
    public function disconnect()
    {
        if ($this->link === null || ! $this->link instanceof \PDO) {
            throw new \RuntimeException("Unable to disconnect unconnected adapter");
        }
        unset($this->link);
        $this->link = null;
        
        return $this;
    }

    /**
     *
     *
     * Prepares a query for execution PDO style
     *
     * @access public
     * @see PDO::prepare()
     * @param $query string
     *            An SQL query to prepare.
     * @param $options array
     *            (Optional) Options to pass to the query
     * @throws \RuntimeException in case of error
     * @return \Wave\Database\Engine Current instance on success
     *        
     */
    public function prepare($query, $options = array())
    {
        try {
            $this->connect();
            $this->stmt = $this->link->prepare($query, $options);
        } catch (\PDOException $ex) {
            throw new \RuntimeException($ex->getMessage(), null, $ex);
        }
        
        return $this;
    }

    /**
     * Execute a query
     *
     * @access public
     * @param array $params
     *            Array of prepared values
     * @see PDO::execute
     * @throws \RuntimeException
     */
    public function execute($params = array())
    {
        $this->connect();
        if (! $this->stmt instanceof \PDOStatement) {
            throw new \RuntimeException("No statement has been prepared", - 1);
        }
        
        return $this->stmt->execute($params);
    }

    /**
     *
     * @see \Wave\Database\Adapter\AbstractAdapter::fetch()
     */
    public function fetch()
    {
        $this->connect();
        if ($this->stmt == null || !method_exists($this->stmt, 'fetch')) {
            throw new \RuntimeException("No statement prepared for exectution");
        }
        return $this->stmt->fetch();
    }

    /**
     *
     * @see \Wave\Database\Adapter\AbstractAdapter::fetchAll()
     */
    public function fetchAll()
    {
        $this->connect();
        return $this->stmt->fetchAll();
    }

    /**
     * Returns the last inserted ID
     *
     * @return int The ID of the last insert
     * @see \PDO::lastInsertId
     */
    public function lastInsertId($name = null)
    {
        $this->connect();
        return $this->link->lastInsertId($name);
    }

    /**
     *
     * @see \Wave\Database\Adapter\AbstractAdapter::bindParam()
     */
    public function bindParam($key, $value, $type)
    {
        $this->stmt->bindParam($key, $value, $type);
        
        return $this;
    }

    /**
     *
     * @see \Wave\Database\Adapter\AbstractAdapter::getQuery()
     */
    public function getQuery()
    {
        return $this->stmt->queryString;
    }
}
