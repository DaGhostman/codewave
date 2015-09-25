<?php

namespace Wave\Framework\Http\Entities\Url;

use Wave\Framework\Exceptions\InvalidKeyException;
use Wave\Framework\Interfaces\Http\QueryInterface;

/**
 * Class Query
 * @package Wave\Framework\Http\Entities\Url
 */
class Query implements QueryInterface, \Countable
{
    private $parts = [];

    /**
     * @param mixed $query array or string
     */
    public function __construct($query = [])
    {
        if (is_array($query)) {
            $this->parts = $query;
        } else {
            parse_str($query, $this->parts);
        }
    }

    /**
     * Return a parameter with $name
     *
     * @param string $name
     * @throws InvalidKeyException
     * @return mixed
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->parts)) {
            throw new InvalidKeyException(sprintf(
                'Unable to fetch "%s", the key does not exists',
                $name
            ));
        }

        return $this->parts[$name];
    }

    /**
     * Add a new parameter
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @internal param string $name
     */
    public function set($key, $value)
    {
        $self = clone $this;
        $self->parts[$key] = $value;

        return $self;
    }

    /**
     * Check if a parameter exists
     *
     * @param string $key
     * @return mixed
     * @internal param string $name
     *
     */
    public function has($key)
    {
        return array_key_exists($key, $this->parts);
    }

    /**
     * Remove a parameter
     * @throws InvalidKeyException
     * @param string $name
     */
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

    public function __toString()
    {
        $query = [];
        foreach ($this->parts as $index => $value) {
            $query[] = $index . '=' . urlencode($value);
        }

        return implode('&', $query);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->parts);
    }
}
