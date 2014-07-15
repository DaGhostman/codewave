<?php
namespace Wave\Database\Adapter;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractAdapter
{

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
    abstract public function connect();

    /**
     * Closes the connectino to the database
     */
    abstract public function disconnect();

    /**
     * Should prepare the query for execution,
     * and return the newly configured object.
     * PDO Style
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
    abstract public function prepare($query, $options = array());

    /**
     * Returns the current prepared query
     *
     * @see PDOStatement::$queryStrting
     */
    abstract public function getQuery();

    /**
     * Execute a query
     *
     * @access public
     * @param array $params
     *            Array of prepared values
     * @see \PDO::execute
     * @throws \RuntimeException
     */
    abstract public function execute($params = array());

    /**
     * Returns the last inserted ID
     *
     * @return int The ID of the last insert
     * @see \PDO::lastInsertId
     */
    abstract public function lastInsertid($name);

    /**
     * Fethces the first result that matches
     * the executed query criteria.
     *
     * @return mixed Returns the result
     */
    abstract public function fetch();

    /**
     * Returns all matching results
     */
    abstract public function fetchAll();

    /**
     * Binds values to query parameters
     */
    abstract public function bindParam($key, $value, $type);
}
