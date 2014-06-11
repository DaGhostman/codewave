<?php

namespace Wave\Application;


/**
 * Environment
 * @package Wave
 * @author  Dimitar Dimitrov
 * @since   1.0.0
 */
class Environment implements \ArrayAccess, \Serializable
{
    /**
     * @var array Container for environment variables
     */
    protected $storage = array();
    
    /**
     * Creates the environment storage, which holds meta data
     * 
     * @param array $bootstrap Settings to set on creation
     */
    public function __construct($bootstrap = array())
    {
        $this->storage = $bootstrap;
    }
    
    /**
     * A magic getter method, retrieves values from the environment
     * 
     * @param string $key Key of the entry
     * @return mixed The value coresponding to the key, or null
     */
    public function __get($key)
    {
        return $this->storage[$key];
    }
    
    /**
     * Magic setter method, adds values to the environment
     * 
     * @param string $key Key to set
     * @param mixed $value The value to set
     * @return null
     */
    public function __set($key, $value)
    {
        $this->storage[$key] = $value;
    }
    
    /**
     * The magic function resolving the isset funciton
     * 
     * @param string $key
     * @return bool True if set, false otherwise
     */
    public function __isset($key)
    {
        return isset($this->storage[$key]);
    }
    
    /**
     * Unsets value from the environment
     * 
     * @param string $key To unset
     * @return null
     */
    public function __unset($key)
    {
        unset($this->storage[$key]);
    }
    
    // ArrayAccess
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->storage);
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->storage[$offset];
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $this->storage[$offset] = $value;
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        unset($this->storage[$offset]);
    }
    
    // Serializeable
    
    /**
     * (non-PHPdoc)
     * @see Serializable::serialize()
     */
    public function serialize()
    {
        return serialize($this->storage);
    }
    
    /**
     * (non-PHPdoc)
     * @see Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        $this->storage = unserialize($serialized);
    }
}
