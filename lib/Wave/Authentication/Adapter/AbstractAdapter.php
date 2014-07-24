<?php
namespace Wave\Authentication\Adapter;

/**
 * AbstractAdapter
 * 
 * @package Wave
 * @author Dimitar Dimitrov
 * @since 1.0.0
 */
abstract class AbstractAdapter
{

    /**
     * Gets the identity record from the database.
     *
     * @param string $authString
     *            Concatenated authentication string.
     * @return array Array containing the identity of the record
     */
    abstract public function get($authString);
}
