<?php
namespace Wave\Database;

class Engine
{
    const ADAPTER_MYSQL  = 'mysql';
    const ADAPTER_PGSQL  = 'pgsql';
    const ADAPTER_SQLITE = 'sqlite';
    
    protected $dsn = null;
    protected $link = null;
    protected $stmt = null;
    protected $result = null;
    
    public $username = null;
    public $password = null;
    
    private $handler = '\Wave\Database\Result';
    
    
    /**
     * Constructs the DNS string required for connecting to the database
     * it does not perform the actual connection, but rather wiats for a
     * request to be made towards the database and initiates a the
     * connection. Note that this proces only happens once per object
     * instantiation.
     *
     *  @access public
     *  
     *  @param array $config Assoc array with settings for the connection
     *          'hostname' Host of the database, filename for SQLite or socket
     *          'database' Database name, if using RDBMS
     *          'username' (Optional) Username
     *          'password' (Optional) Password
     *          'port' (Optional) Port to use when connecting
     * 
     * @param array $options Array with valid PDO options
     * 
     * @throws \InvalidArgumentException
     *
     */
    public function __construct($config, $options = array())
    {
        foreach ($config as $key => $value) {
            $$key = $value;
        }
        if ($engine == self::ADAPTER_MYSQL) {
            $this->dsn = sprintf(
                "mysql:host=%s;dbname=%s;port=%s",
                $hostname,
                $database,
                (isset($port) ? $port : 3306)
            );
        } elseif ($engine == self::ADAPTER_PGSQL) {
            $this->dsn = sprintf(
                "pgsql:host=%s;dbname=%s;port=%s",
                $hostname,
                $database,
                (isset($port) ? $port : 3306)
            );
        } elseif ($engine == self::ADAPTER_SQLITE) {
            $this->dsn = sprintf(
                "sqlite:%s",
                $database
            );
        } else {
            throw new \InvalidArgumentException(
                "Unknown adapter specified"
            );
        }
        
        $this->password = (isset($password) ? $password : $this->password);
        $this->username = (isset($username) ? $username : $this->username);
    }
    
    
    /**
     * Checks if a connecting is established, if not it establishes it,
     * otherwise it returns.
     *
     * @access public
     * 
     * @return boolean True if connected, false otherwise
     * @throws \RuntimeException If unable to connect
     *
     */
    public function connect()
    {
        if ($this->link && $this->link instanceof \PDO) {
            return true;
        }
        
        try {
            $this->link = new \PDO(
                $this->dsn,
                $this->username,
                $this->password,
                $options
            );
        } catch (\PDOException $ex) {
            throw new \RuntimeException(
                "Unable to instantiate PDO connection",
                $ex->getCode(),
                $ex
            );
        }
    }
    /**
     * Returns the PDO instance or null if not connected
     * 
     * @return mixed \PDO object when connected, null otherwise
     */
    public function getLink()
    {
        return $this->link;
    }
    
    /**
     *
     * Prepares a query for execution PDO style
     *
     * @access public
     * @see PDO::prepare()
     * @param $query string An SQL query to prepare.
     * @param $options array (Optional) Options to pass to the query
     * @throws \RuntimeException in case of error
     * @return \Wave\Database\Engine Current instance on success
     *
     */
    public function prepare($query, $options)
    {
        try {
            $this->connect();
            $this->stmt = $this->link->prepare($query, $options);
        } catch (\PDOException $ex) {
            throw new \RuntimeException(
                sprintf("Unable to prepare the query '%s'", $query),
                $ex->getCode(),
                $ex
            );
        }
        
        return $this;
    }
    
    /**
     * Returns the \PDOStatement of the last 'prepare' call
     * @return mixed null or \PDOStatement
     */
    public function getStatement()
    {
        return (!is_null($this->stmt)) ? $this->stmt : null;
    }
    
    
    /**
     * Execute a query
     * 
     * @access public
     * @param array $params Array of prepared values
     * @see \PDO::execute
     * @throws \RuntimeException
     */
    public function execute($params = array())
    {
        try {
            $this->connect();
            if (!$this->getStatement() instanceof \PDOStatement) {
                throw new \RuntimeException(
                    "No statement has been prepared",
                    -1
                );
            }
            $this->getStatement()->execute($params);
        } catch (\PDOException $ex) {
            throw new \RuntimeException(
                sprintf(
                    "Unable to execute '%s' with params [%s]",
                    $this->getStatement()->queryString
                ),
                $ex->getCode(),
                $ex
            );
        }
    }
    
    public function setResulthandler($handler)
    {
        $this->handler = $handler;
        
        return $this;
    }
    
    /**
     * Returns the last inserted ID
     * 
     * @return int The ID of the last insert
     * @see \PDO::lastInsertId
     */
    public function getLastId($name = null)
    {
        try {
            $this->connect();
            $this->getLink()->lastInsertId($name);
        } catch (\PDOException $ex) {
            throw new \RuntimeException(
                "Unable to retrieve lastInsertId",
                $ex->getCode(),
                $ex
            );
        }
    }
    
    public function fetch()
    {
        try {
            $this->connect();
            $result = $this->getStatement()->fetch();
        } catch (\PDOException $ex) {
            throw new \RuntimeException(
                "Unable to fetch result".
                $ex->getCode(),
                $ex
            );
        }
        
        return new $this->handler($result);
    }
    
    public function fetchAll()
    {
        try {
            $this-->connect();
        } catch (\PDOException $ex) {
            throw new \RuntimeException(
                "Unable to fetch resuts",
                $ex->getCode(),
                $ex
            );
        }
    }
}
