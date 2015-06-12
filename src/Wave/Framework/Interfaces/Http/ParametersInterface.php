<?php


namespace Wave\Framework\Interfaces\Http;

interface ParametersInterface
{
    //public function __construct(RequestInterface $request);

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
     * Returns an array with all the parameters
     *
     * @return array
     */
    public function export();
}
