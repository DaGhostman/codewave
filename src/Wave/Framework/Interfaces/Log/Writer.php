<?php

namespace Wave\Framework\Interfaces;

interface Writer
{
    public function __construct($resource);

    /**
     * @param $string
     * @param int|null $length
     * @return $this
     */
    public function writeLine($string, $length = null);
    public function lock();
    public function unlock();
}
