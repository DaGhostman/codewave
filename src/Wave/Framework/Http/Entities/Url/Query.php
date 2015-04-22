<?php

namespace Wave\Framework\Http\Entities\Url;

use Wave\Framework\Http\Exceptions\InvalidKeyException;

class Query implements \Countable
{
    private $parts = [];

    /**
     * Parses the $query and stores it internally as an array.
     * all elements can be accessed using 'get' method
     * and 'set' for updating/adding new ones.
     *
     * @param $query string|array The url query
     */
    public function __construct($query = '')
    {
        if (is_array($query)) {
            $this->parts = $query;
        } else {
            parse_str($query, $this->parts);
        }
    }

    /**
     * Returns entry in the query based on
     *
     * @param $key string name of the entry
     * @return mixed value of the entry
     * @throws \Wave\Framework\Http\Exceptions\InvalidKeyException
     */
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

    /**
     * Creates a new instance of Query and sets/updates
     * the new value in it and returns it
     *
     * @param $key string
     * @param $value string
     * @return Query
     */
    public function set($key, $value)
    {
        $self = clone $this;
        $self->parts[$key] = $value;

        return $self;
    }

    /**
     * Check if the a specific $key exists
     *
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->parts);
    }

    /**
     * @param $key
     * @throws \Wave\Framework\Http\Exceptions\InvalidKeyException
     */
    public function remove($key)
    {
        if (!$this->has($key)) {
            throw new InvalidKeyException(sprintf(
                'Unable to remove, non-existing entry "%s"',
                $key
            ));
        }

        unset($this->parts[$key]);
    }

    /**
     * Creates a new Query object with the merged
     * parameters and returns it.
     *
     * @param $entities array key => value pairs
     * @return Query
     */
    public function import($entities)
    {
        $self = clone $this;
        $self->parts = array_merge($self->parts, $entities);

        return $self;
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
}
