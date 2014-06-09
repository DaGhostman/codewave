<?php
namespace Wave\Database;

class Engine
{

    protected $result = null;

    protected $link = null;

    private $handler = '\ArrayObject';

    public function __construct($connection)
    {
        if (!$connection instanceof Adapter\AbstractAdapter) {
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

    public function setResulthandler($handler)
    {
        $this->handler = $handler;
        
        return $this;
    }

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

    public function handler($set)
    {
        return new $this->handler($set);
    }

    public function insert($table, $binds)
    {
        $cols = implode(', ', $binds);
        $keys = implode(',:', $binds);
        
        $query = "INSERT INTO %s (%s) VALUES %s";
        
        $this->link->prepare(sprintf($query, $table, $cols, $keys));
        
        return $this;
    }

    public function select($table, $fields)
    {
        $query = "SELECT % FROM %s";
        $this->link->prepare(sprintf($query, implode(',', $fields), $table));
        
        return $this;
    }

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

    public function delete($table)
    {
        $this->link->prepare(sprintf("DELETE FROM %s", $table));
        
        return $this;
    }

    public function where($clause)
    {
        $this->link->prepare(sprintf('%s WHERE %s', $this->link->getQuery(), $clause));
        
        return $this;
    }

    public function custom($sql)
    {
        $this->link->prepare('%s %s', $this->link->getQuery(), $sql);
        
        return $this;
    }

    public function bind($key, $value, $type = null)
    {
        $this->link->bindParam($key, $value, $type);
        
        return $this;
    }

    public function execute($params = array())
    {
        $this->link->execute($params);
        
        return $this;
    }
}
