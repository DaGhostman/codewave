<?php
namespace Wave\Framework\Interfaces\Http;

/**
 * Interface QueryInterface
 *
 * @package Wave\Framework\Interfaces\Http
 */
interface QueryInterface
{

    /**
     * Import a new set parameters. The new parameters should be
     * appended to the current list and not replacing one for the other.
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function import(array $parameters);

    public function __toString();
}
