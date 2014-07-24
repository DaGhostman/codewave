<?php
namespace Wave\Session\Container;

// Updates itself on provider change
use Wave\Pattern\Observer\Observer;

/**
 * Class AbstractContainer
 * @package Wave\Session\Container
 * @deprecated
 */
abstract class AbstractContainer extends Observer implements \ArrayAccess
{
    /**
     * @param $data
     *
     * @return mixed
     */
    abstract public function populate($data);

    /**
     * @return mixed
     */
    abstract public function getAdapter();

    /**
     * @param $adapter
     *
     * @return mixed
     */
    abstract public function setAdapter($adapter);
}
