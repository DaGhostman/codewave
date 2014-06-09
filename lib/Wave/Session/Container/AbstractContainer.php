<?php
namespace Wave\Session\Container;

// Updates itself on provider change
use \Wave\Pattern\Observer\Observer;

abstract class AbstractContainer extends Observer implements \ArrayAccess
{
    // Sets the data, on instantiation
    abstract public function populate($data);
    
    // Return the adapter
    abstract public function getAdapter();
    
    abstract public function setAdapter($adapter);
}
