<?php
namespace Wave\Session\Container;

class BasicContainer extends AbstractContainer
{

    private $storage = array();

    protected $adapter;

    /**
     * (non-PHPdoc)
     * 
     * @see \Wave\Session\Container\AbstractContainer::populate()
     */
    public function populate($data)
    {
        $this->storage = array_merge($this->storage, $data);
        
        return $this;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Wave\Session\Container\AbstractContainer::setAdapter()
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        $this->populate($adapter->fetch());
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Wave\Session\Container\AbstractContainer::getAdapter()
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Magic setter
     *
     * @param string $key            
     * @param mixed $value            
     */
    public function __set($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /**
     * Magic Getter
     *
     * @param string $key            
     * @return mixed
     */
    public function __get($key)
    {
        return $this->storage[$key];
    }

    /**
     * Magic isset
     * 
     * @param string $key            
     * @return boolean True - exists, false - otherwise
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * Magic unset
     *
     * @param string $key
     *            The key to unset
     */
    public function __unset($key)
    {
        unset($this[$key]);
    }
    
    // ArrayAccess
    
    /**
     * (non-PHPdoc)
     * 
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->storage[$offset];
    }

    /**
     * (non-PHPdoc)
     * 
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $this->storage[$offset] = $value;
        $this->adapter->update($this->storage);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        unset($this->storage[$offset]);
        $this->adapter->update($this->storage);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->storage);
    }
}
