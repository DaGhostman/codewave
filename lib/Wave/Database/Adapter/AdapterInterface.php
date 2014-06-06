<?php
namespace Wave\Database\Adapter;

/**
 * @codeCoverageIgnore
 */
interface AdapterInterface
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
    public function connect();

    public function disconnect();

    /**
     *
     *
     *
     *
     * Should prepare the query for execution,
     * and return the newly configured object. PDO Style
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
    public function prepare($query, $options);

    /**
     * Returns the current prepared query
     *
     * @see PDOStatement::$queryStrting
     */
    public function getQuery();

    /**
     * Execute a query
     *
     * @access public
     * @param array $params
     *            Array of prepared values
     * @see \PDO::execute
     * @throws \RuntimeException
     */
    public function execute($params = array());

    /**
     * Returns the last inserted ID
     *
     * @return int The ID of the last insert
     * @see \PDO::lastInsertId
     */
    public function lastInsertid($name);

    /**
     * Fethces the first result that matches
     * the executed query criteria.
     *
     * @return mixed Returns the result
     */
    public function fetch();

    /**
     * Returns all matching results
     */
    public function fetchAll();

    /**
     * Binds values to query parameters
     */
    public function bindParam($key, $value, $type);
}
