<?php
namespace Wave\Framework\Interfaces\Http;

/**
 * Interface QueryInterface
 *
 * @package Wave\Framework\Interfaces\Http
 */
interface QueryInterface
{
    /**
     * Allows constructor injection of the query string,
     * or already parsed array with parameters.
     *
     * @param string|array $query
     */
    public function __construct($query);

    /**
     * Return a parameter with $name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Add a new parameter
     *
     * @param string $name
     * @param mixed $value
     *
     * @return mixed
     */
    public function set($name, $value);

    /**
     * Check if a parameter exists
     *
     * @param string $name
     *
     * @return mixed
     */
    public function has($name);

    /**
     * Remove a parameter
     *
     * @param string $name
     */
    public function remove($name);

    /**
     * Import a new set parameters. The new parameters should be
     * appended to the current list and not replacing one for the other.
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function import(array $parameters);

    public function __toString();
}