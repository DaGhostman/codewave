<?php
namespace Wave\Database;

class Engine
{

    protected $result = null;

    protected $link = null;

    private $handler = '\ArrayObject';

    /**
     * Injects the adapter to use for connection
     *
     * @param \Wave\Database\Adapter\AbstractAdapter $connection            
     * @throws \InvalidArgumentException
     */
    public function __construct($connection)
    {
        if (! $connection instanceof Adapter\AbstractAdapter) {
            throw new \InvalidArgumentException("Invalid database connection passed", - 1);
        }
        
        $this->link = $connection;
    }

    /**
     * Returns the PDO instance or null if not connected
     *
     * @return mixed The current adapter object when connected, null otherwise
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Classname to handle the result.
     * Handler must expect the result object in the constructor.
     *
     * @param string $handler            
     * @return \Wave\Database\Engine
     */
    public function setResulthandler($handler)
    {
        $this->handler = $handler;
        
        return $this;
    }

    /**
     * Returns the resultset
     *
     * @param string $handler
     *            handler to override the default one
     * @param bool $max
     *            if requiered more than one result.
     * @throws \RuntimeException
     * @return mixed Instance of the Hanlder
     */
    public function fetch($handler = null, $max = false)
    {
        if ($handler !== null) {
            $this->setResulthandler($handler);
        }
        
        try {
            if ($max === false) {
                $result = $this->link->fetch();
            } else {
                $result = $this->link->fetchAll();
            }
        } catch (\RuntimeException $ex) {
            throw new \RuntimeException("Unable to fetch result", $ex->getCode(), $ex);
        }
        
        return new $this->handler($result);
    }

    /**
     * Creates the handler and returns it
     *
     * @param array $set
     *            the result set
     * @return mixed
     */
    public function handler($set)
    {
        return new $this->handler($set);
    }

    /**
     * Wrapper to provide basic operation INSERT
     *
     * @param string $table
     *            The name of the table to use
     * @param array $binds
     *            Assoc array. the key should be the colum name and value the named parameter
     * @return \Wave\Database\Engine
     */
    public function insert($table, $binds)
    {
        $cols = implode(', ', $binds);
        $keys = implode(',:', $binds);
        
        $query = "INSERT INTO %s (%s) VALUES %s";
        
        $this->link->prepare(sprintf($query, $table, $cols, $keys));
        
        return $this;
    }

    /**
     * Implementation of SELECT
     *
     * @param string $table
     *            The table to use
     * @param array $fields
     *            numeric array with the fields to fetch
     * @return \Wave\Database\Engine
     */
    public function select($table, $fields)
    {
        $query = "SELECT %s FROM %s";
        $this->link->prepare(sprintf($query, implode(',', $fields), $table));
        
        return $this;
    }

    /**
     * Implementation of UPDATE
     *
     * @param string $table
     *            The table name
     * @param array $binds
     *            Numeric array with values which will be used as named parameters
     * @return \Wave\Database\Engine
     */
    public function update($table, $binds)
    {
        $query = "UPDATE %s SET %s";
        
        $sets = array();
        foreach ($binds as $set) {
            array_push($sets, sprintf("%s = :%s", $set, $set));
        }
        
        $this->link->prepare(sprintf($query, $table, implode(', ', $sets)));
        
        return $this;
    }

    /**
     * Implementation of the DELETE
     *
     * @param string $table
     *            The table to use
     * @return \Wave\Database\Engine
     */
    public function delete($table)
    {
        $this->link->prepare(sprintf("DELETE FROM %s", $table));
        
        return $this;
    }

    /**
     * Appends WHERE to the currently prepared query.
     * Write
     * you conditions without adding the 'WHERE' keyword
     *
     * @param string $clause
     *            the contents of the where clause
     * @return \Wave\Database\Engine
     */
    public function where($clause)
    {
        $this->link->prepare(sprintf('%s WHERE %s', $this->link->getQuery(), $clause));
        
        return $this;
    }

    /**
     * Append custom SQL string to the query being prepared
     *
     * @param string $sql
     *            The SQL query to append
     * @return \Wave\Database\Engine
     */
    public function custom($sql)
    {
        $this->link->prepare('%s %s', $this->link->getQuery(), trim($sql));
        
        return $this;
    }

    /**
     * Binds the $value to the $key
     *
     * @param string $key
     *            name of parameter to bind to
     * @param mixed $value
     *            The value to bind
     * @param mixed $type
     *            \PDO::PARAM_* constants
     * @return \Wave\Database\Engine
     */
    public function bind($key, $value, $type = null)
    {
        $this->link->bindParam($key, $value, $type);
        
        return $this;
    }

    /**
     * Executes the query
     *
     * @param array $params
     *            arguments to pass
     * @return \Wave\Database\Engine
     */
    public function execute($params = array())
    {
        $this->link->execute($params);
        
        return $this;
    }
}
