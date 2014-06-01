<?php

namespace Wave;

use \Symfony\Component\EventDispatcher;

/** 
 * @author phpAcorn <phpacorn@gmail.com>
 * @copyright phpAcorn 2014
 * @link http://phpacorn/
 * @package phpAcorn\Wave
 * @version 1.0
 * @name Event
 * @uses \Wave\Pattern\Observer\Subject
 * @uses \Symfony\Component\EventDispatcher\Event
 * @see \Symfony\Component\EventDispatcher\Event
 */
class Event extends EventDispatcher\Event
{
    /**
     * @access protected
     * @var array The registry storage of the object
     */
    protected $data = array();
    
    /**
     * A magic function to represent dynamic getters and setters
     * instead of assigning everything to variables inside the object.
     * 
     * @param string $method The called method, ie getEnvironment, etc.
     * @param mixed $argument Set only if a setter is called
     * 
     * @return mixed getter value or false, instance for chaining
     */
    public function __call ($method, $argument = null)
    {
        $action = strtolower(substr($method, 0, 3));
        $key = strtolower(substr($method, 3));
        
        if ('get' === $action) {
            return $this->$key;
        } elseif ('set' === $action) {
            $this->$key = $argument[0];
        }
        
        return $this;
    }
    
    /**
     * @access public
     *
     * @param string $key The key to store
     * @param mixed $value The value of the key
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    /**
     * @access public
     * 
     * @param string $key The key for a value to be retrieved.
     * @return mixed The value of the key, false otherwise
     */
    public function __get($key)
    {
        return (array_key_exists($key, $this->data)) ?
            $this->data[$key] : false;
    }
    
    public function __isset($key)
    {
        return array_key_exists($key, $this->data);
    }
}
