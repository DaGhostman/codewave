<?php


namespace Wave\Storage;


class Registry implements \Countable, \Serializable, \ArrayAccess
{

    protected $storage = array();
    protected $mutable = true;
    protected $override = true;

    protected $persistent = false;

    protected $name = null;

    public function __construct($options = array())
    {
        $this->mutable = (isset($options['mutable']) ?
            $options['mutable'] : true);

        $this->override = (isset($options['override']) ?
            $options['override'] : true);


        if (array_key_exists('data', $options) && !empty($options['data'])) {
            $this->storage = $options['data'];
        }
    }

    public function set($key, $value)
    {
        if ($this->mutable) {
            if ($this->override) {
                $this->storage[$key] = $value;
            } else {
                if (isset($this->storage[$key])) {
                    return false;
                }

                $this->storage[$key] = $value;
            }
        } else {
            throw new \RuntimeException("Unable to modify immutable registry");
        }
    }

    public function remove($key)
    {
        if ($this->isMutable()) {
            unset($this->storage[$key]);
        } else {
            throw new \RuntimeException("Unable to modify immutable registry");
        }
    }

    public function get($key)
    {
        if (!array_key_exists($key, $this->storage)) {
            return null;
        }


        return $this->storage[$key];
    }

    public function exists($key)
    {
        return array_key_exists($key, $this->storage);
    }

    public function isMutable()
    {
        return $this->mutable;
    }

    public function isOverridable()
    {
        return $this->override;
    }

    public function count()
    {
        return count($this->storage);
    }

    public function serialize()
    {
        return serialize(array(
            'mutable' => $this->isMutable(),
            'override' => $this->isOverridable(),
            'storage' => $this->storage
        ));
    }

    public function unserialize($data)
    {
        foreach (unserialize($data) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function offsetExists($key)
    {
        return $this->exists($key);
    }

    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    public function __isset($key)
    {
        return $this->exists($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __unset($key)
    {
        $this->remove($key);
    }
}
