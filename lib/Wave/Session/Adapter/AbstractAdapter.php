<?php
namespace Wave\Session\Adapter;

/**
 * Class AbstractAdapter
 * @package Wave\Session\Adapter
 * @deprecated
 */
abstract class AbstractAdapter
{

    /**
     * Reads the file and returns the contents
     *
     * @return array Returns the contents of the file
     */
    abstract public function fetch();

    /**
     *
     * @param string $source
     *            the source path
     */
    abstract public function __construct($source);

    /**
     * Updates the file with the new contents
     */
    abstract public function update($data);
}
