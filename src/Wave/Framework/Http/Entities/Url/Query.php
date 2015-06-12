<?php

namespace Wave\Framework\Http\Entities\Url;

use Wave\Framework\Exceptions\InvalidKeyException;
use Wave\Framework\Interfaces\Http\ParametersInterface;
use Wave\Framework\Interfaces\Http\QueryInterface;

class Query implements ParametersInterface, QueryInterface, \Countable, \Serializable
{
    private $parts = [];

    /**
     * Allows constructor injection of the query string,
     * or already parsed array with parameters.
     *
     * @param string|array $query
     */
    public function __construct($query = '')
    {
        if (is_array($query)) {
            $this->parts = $query;
        } else {
            parse_str($query, $this->parts);
        }
    }

    public function get($key)
    {
        if (!array_key_exists($key, $this->parts)) {
            throw new InvalidKeyException(sprintf(
                'Unable to fetch "%s", the key does not exists',
                $key
            ));
        }

        return $this->parts[$key];
    }

    public function set($key, $value)
    {
        $self = clone $this;
        $self->parts[$key] = $value;

        return $self;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->parts);
    }

    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new InvalidKeyException(sprintf(
                'Unable to remove, non-existing entry "%s"',
                $name
            ));
        }

        unset($this->parts[$name]);
    }

    /**
     * Creates a new Query object with the merged
     * parameters and returns it.
     *
     * @param $entities array key => value pairs
     * @return Query
     */
    public function import(array $entities)
    {
        $self = clone $this;
        $self->parts = array_merge($self->parts, $entities);

        return $self;
    }

    public function export()
    {
        return $this->parts;
    }

    public function __toString()
    {
        $query = [];
        foreach ($this->parts as $index => $value) {
            $query[] = $index . '=' . urlencode($value);
        }

        return implode('&', $query);
    }

    public function count()
    {
        return count($this->parts);
    }

    public function serialize()
    {
        return serialize($this->parts);
    }

    public function unserialize($string)
    {
        $this->parts = unserialize($string);
    }
}
