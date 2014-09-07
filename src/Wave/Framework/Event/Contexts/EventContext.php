<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/08/14
 * Time: 23:25
 */

namespace Wave\Framework\Event\Contexts;


use Wave\Framework\Application\Interfaces\ContextInterface;

class EventContext implements ContextInterface
{

    protected $scope;
    protected $store;

    public function scope()
    {
        return $this->scope;
    }

    /**
     * Sets the caller for later access by objects
     *
     * @param $scope object The calling class
     */
    public function __construct($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Adds an entity to the context, identified by a key
     *
     * @param $key   string Key of the entity
     * @param $value mixed Value of the entity
     *
     * @return $this
     */
    public function push($key, $value)
    {
        $this->store[$key] = $value;
    }

    /**
     *
     * Retrieves a entity corresponding to $key
     *
     * @param $key string
     *
     * @return mixed
     */
    public function fetch($key)
    {
        if (array_key_exists($key, $this->store)) {
            return $this->store[$key];
        }

        return null;
    }
}
