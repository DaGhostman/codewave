<?php

namespace Wave\Application;

class Environment implements \ArrayAccess, \Serializable
{
    protected $storage = array();
    public function __construct($bootstrap = array())
    {
        $this->storage = $bootstrap;
    }
    
    public function __get($key)
    {
        return $this->storage[$key];
    }
    
    public function __set($key, $value)
    {
        $this->storage[$key] = $value;
    }
    
    public function __isset($key)
    {
        return isset($this->storage[$key]);
    }
    
    public function __unset($key)
    {
        unset($this->storage[$key]);
    }
    
    // ArrayAccess
    
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->storage);
    }
    
    public function offsetGet($offset)
    {
        return $this->storage[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        $this->storage[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->storage[$offset]);
    }
    
    // Serializeable
    
    public function serialize()
    {
        return serialize($this->storage);
    }
    
    public function unserialize($serialized)
    {
        $this->storage = unserialize($serialized);
    }
}
