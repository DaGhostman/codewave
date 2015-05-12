<?php
namespace Stub;

use Wave\Framework\Interfaces\Http\QueryInterface;

class MockQuery implements QueryInterface
{

    private $params = [];
    /**
     * Allows constructor injection of the query string,
     * or already parsed array with parameters.
     *
     * @param string|array $query
     */
    public function __construct($query = null) {}

    /**
     * Return a parameter with $name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->params[$name];
    }

    /**
     * Add a new parameter
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function set ($name, $value)
    {
        // TODO: Implement set() method.
    }

    /**
     * Check if a parameter exists
     *
     * @param string $name
     *
     * @return mixed
     */
    public function has ($name) {}

    /**
     * Remove a parameter
     *
     * @param string $name
     */
    public function remove ($name) {}

    /**
     * Import a new set parameters. The new parameters should be
     * appended to the current list and not replacing one for the other.
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function import (array $parameters)
    {
        $this->params = $parameters;

        return $this;
    }

    public function __toString ()
    {
        return http_build_query($this->params);
    }
}