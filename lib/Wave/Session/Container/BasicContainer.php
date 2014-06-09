<?php

namespace Wave\Session\Container;

class BasicContainer extends AbstractContainer
{
    private $storage = array();
    protected $adapter;
    
    public function populate($data)
    {
        $this->storage = array_merge($this->storage, $data);
        
        return $this;
    }
    
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        $this->populate($adapter->fetch());
    }
    
    public function getAdapter()
    {
        return $this->adapter;
    }
    
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
    
    public function __get($key)
    {
        return $this[$key];
    }
    
    public function __isset($key)
    {
        return array_key_exists($key, $this);
    }
    
    public function __unset($key)
    {
        unset($this[$key]);
    }
    
    // ArrayAccess
    
    public function offsetGet($offset)
    {
        return $this->storage[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        $this->storage[$offset] = $value;
        $this->adapter->update($this->storage);
    }
    
    public function offsetUnset($offset)
    {
        unset($this->storage[$offset]);
        $this->adapter->update($this->storage);
    }
    
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->storage);
    }
}