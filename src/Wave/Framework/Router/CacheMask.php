<?php
namespace Wave\Framework\Router;

class CacheMask
{
    private $fetch = 'fetch';
    private $save = 'save';
    private $contains = 'contains';

    protected $cacheProvider;

    /**
     * @param object $cacheProvider
     * @param array $map (Optional) This entry should have assoc array, which is used
     *                   to map the 'fetch', 'contains' and 'save' methods to the
     *                   respectful methods in the $cacheProvider
     */
    public function __construct($cacheProvider, array $map = [])
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * Must get an entry identified by $key from the $cacheProvider
     *
     * @param $key
     *
     * @return mixed
     */
    public function fetch($key)
    {
        return call_user_func([$this->cacheProvider, $this->fetch], $key);
    }

    /**
     * Must perform a check to see if the entry with $key exists in the cache
     *
     * @param $key
     *
     * @return mixed
     */
    public function contains($key)
    {
        return call_user_func([$this->cacheProvider, $this->contains], $key);
    }

    /**
     * Must save the $value in the cache identified by the $key and optionally handle
     * cache $ttl. By default if $ttl is not provided it is considered null, the cache
     * provider you are using should consider this and handle it accordingly
     *
     * @param     $key
     * @param     $value
     * @param int $ttl
     *
     * @return mixed
     */
    public function save($key, $value, $ttl = 0)
    {
        return call_user_func([$this->cacheProvider, $this->save], $key, $value, $ttl);
    }
}
