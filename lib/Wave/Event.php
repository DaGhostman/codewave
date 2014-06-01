<?php

namespace Wave;

use \Symfony\Component\EventDispatcher;

class Event extends EventDispatcher\Event 
{
    protected $data = array();
    
    /**
     * A magick function to represent dynamic getters and setters
     * instead of assigning everything to variables inside the object.
     * 
     * @param string $method The called method, ie getEnvironment, etc.
     * @param mixed $argument Set only if a setter is called
     * 
     * @return mixed getter value or false, instance for chaining
     */
    public function __call ($method, $argument = null) {
        $action = substr($method, 0, 3);
        $key = substr($method, 3);
        
        switch(strtolower($action))
        {
            case 'get':
                return call_user_func_array(array($this, '__get'), 
                    array($key));
                
                break;
            case 'set':
                call_user_func_array(array($this, "__set"), 
                    array($key, $argument[0]));
                    
                break;
        }
        
        return $this;
    }
    
    /**
     * @access public
     *
     * @param string $key The key to store
     * @param mixed $value The value of the key
     */
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }
    
    /**
     * @access public
     * 
     * @param string $key The key for a value to be retrieved.
     * @return mixed The value of the key, false otherwise
     */
    public function __get($key) {
        return (array_key_exists($key, $this->data)) ? 
            $this->data[$key] : false;
    } 
}
