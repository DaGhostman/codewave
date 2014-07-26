<?php


namespace Wave\Storage;


class Registry implements \Countable, \Serializable, \ArrayAccess
{

    protected $storage = array();
    protected $mutable = true;
    protected $override = true;

    protected $persistent = false;

    protected $name = null;

    /**
     * Setts options for the object and optionally injects data into it.
     *
     * @access public
     * @method Registry::__construct Object constructor
     * @param array $options Options for the registry. Valid keys are:
     *                       mutable - determines if the object is mutable
     *                       override - allows overriding of keys, when object is mutable
     *                       data - array which should be preset (Only way to write when immutable)
     */
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

    /**
     * Adds entry to the registry, if mutable
     *
     * @param $key string key to assign <em>$value</em> to
     * @param $value mixed The value to assign
     *
     * @return null Does not return anything
     * @throws \RuntimeException if trying to modify immutable object
     */
    public function set($key, $value)
    {
        if ($this->mutable) {
            if ($this->override) {
                $this->storage[$key] = $value;
            } else {
                if (isset($this->storage[$key])) {
                    return;
                }

                $this->storage[$key] = $value;
            }
        } else {
            throw new \RuntimeException("Unable to modify immutable registry");
        }
    }

    /**
     * Removes entry from the object
     *
     * @param $key string Key to remove
     *
     * @throws \RuntimeException if trying to modify immutable object
     */
    public function remove($key)
    {
        if ($this->isMutable()) {
            unset($this->storage[$key]);
        } else {
            throw new \RuntimeException("Unable to modify immutable registry");
        }
    }

    /**
     * Returns the value assigned to the <em>$key</em>
     * @param $key string The key to lookup
     *
     * @return mixed Null if key was not found, its value otherwise
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->storage)) {
            return null;
        }


        return $this->storage[$key];
    }

    /**
     * Check if a key exists in the object
     *
     * @param $key string The key to look for
     *
     * @return bool
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * Check if object is mutable
     *
     * @return bool
     */
    public function isMutable()
    {
        return $this->mutable;
    }

    /**
     * Check if object can be overridden
     *
     * @return bool
     */
    public function isOverridable()
    {
        return $this->override;
    }

    /**
     * The number of entries in the object
     * @return int
     */
    public function count()
    {
        return count($this->storage);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            'mutable' => $this->isMutable(),
            'override' => $this->isOverridable(),
            'storage' => $this->storage
        ));
    }

    /**
     * @param string $data
     */
    public function unserialize($data)
    {
        foreach (unserialize($data) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->exists($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param mixed $key
     *
     * @return mixed|null
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param $key
     *
     * @return null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param $key
     */
    public function __unset($key)
    {
        $this->remove($key);
    }
}
