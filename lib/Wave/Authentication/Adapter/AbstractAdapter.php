<?php
namespace Wave\Authentication\Adapter;

abstract class AbstractAdapter
{
    /**
     * Gets the identity reccord from the database.
     * 
     * @param string $auth_string Concated authentication string.
     * @return array Array containing the identity of the reccord
     */
    abstract public function get($auth_string);
}