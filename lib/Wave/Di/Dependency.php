<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 30/07/14
 * Time: 21:47
 */

namespace Wave\Di;

/**
 * Class Dependency
 * @package Wave\Di
 */
class Dependency
{
    protected $object = null;
    protected $master = null;
    protected $blueprint = null;

    /**
     * @param $footprint callable The callable passed to the IoC object.
     * @throws \InvalidArgumentException
     */
    public function __construct($footprint, $master)
    {
        if (!is_object($footprint)) {
            throw new \InvalidArgumentException("Received argument should be object.");
        }

        $this->master = $master;
        $this->blueprint = $footprint;
    }

    public function raw()
    {
        $this->invoke();

        return $this->object;
    }

    /**
     * (Magic function)
     *
     * This function serves as man-in-the-middle for method calls,
     * the if statement there serves for lazy-loading the objects
     * (They get created whenever you call the first method and
     * all later calls use the same instance).
     *
     * @param $name string The method name to invoke
     * @param $args array The array of arguments which will be passed
     *               to the call of the method
     *
     * @return mixed the result of the called method.
     */
    public function __call($name, $args = array())
    {
        $this->invoke();
        if (!$this->object instanceof Dependency) {
            $m = call_user_func_array(
                array($this->master, 'with'),
                array_merge($args, array(null))
            );
            return $m->resolve($this->object, $name);
        }

        return call_user_func_array(array($this->object, $name), $args);
    }



    public function invoke()
    {
        if (is_null($this->object)) {
            if ($this->blueprint instanceof \Closure) {
                $this->object = call_user_func($this->blueprint);
            } else {
                $this->object = $this->blueprint;
            }
        }
    }

    /**
     * @param $key string
     *
     * @return mixed
     */
    public function __get($key)
    {
        $this->invoke();

        return $this->object->{$key};
    }

    /**
     * @param $key string
     * @param $value mixed
     */
    public function __set($key, $value)
    {
        $this->invoke();
        $this->object->$key = $value;
    }
}
