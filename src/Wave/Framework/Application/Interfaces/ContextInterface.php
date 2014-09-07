<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 04/09/14
 * Time: 13:18
 */

namespace Wave\Framework\Application\Interfaces;


interface ContextInterface
{
    /**
     * Sets the caller for later access by objects
     * @param $scope object The calling class
     */
    public function __construct($scope);

    /**
     * Getter for the scope
     * @return mixed
     */
    public function scope();

    /**
     * Adds an entity to the context, identified by a key
     *
     * @param $key string Key of the entity
     * @param $value mixed Value of the entity
     *
     * @return $this
     */
    public function push($key, $value);

    /**
     *
     * Retrieves a entity corresponding to $key
     * @param $key string
     *
     * @return mixed
     */
    public function fetch($key);
}
