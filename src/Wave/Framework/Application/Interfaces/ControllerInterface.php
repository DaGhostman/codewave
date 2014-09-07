<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 04/09/14
 * Time: 13:01
 */

namespace Wave\Framework\Application\Interfaces;


interface ControllerInterface
{
    /**
     * Defines the pattern for the Controller
     *
     * @param string $pattern The pattern to which the controller is assigned
     * @return $this
     */
    public function setPattern($pattern);

    /**
     * Sets the callback which performs the heavy lifting, in
     * custom implementations could be omitted.
     *
     * @param $callback callable Callable which performs the actual execution logic
     *
     * @return $this
     */
    public function action($callback);

    /**
     * Defines the methods for which the controller should respond
     *
     * @param $methods mixed Array with methods or single method as a string
     * @return $this
     */
    public function via();

    /**
     * Calls the callback and passes an assoc array to it containing all arguments
     * @param $data array Array of arguments to pass to the callback
     *
     * @return $this
     */
    public function invoke(array $data = array());

    /**
     * @param $conditions array Assoc array with conditions which have to be met
     *
     * @return $this
     */
    public function conditions(array $conditions);

    /**
     * Checks if the $path matches controllers pattern and conditions
     * @param $path string The current request URI
     *
     * @return bool
     */
    public function match($path);
}
