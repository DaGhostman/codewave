<?php

namespace Wave\Framework\Log;

use Wave\Framework\Interfaces\Writer;

class FileWriter implements Writer
{
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('Provided argument is not a valid resource');
        }

        $this->resource = $resource;
    }

    public function writeLine($string, $length = null)
    {
        fwrite($this->resource, $string, $length);

        return $this;
    }

    public function lock()
    {
        flock($this->resource, LOCK_EX);
    }

    public function unlock()
    {
        flock($this->resource, LOCK_UN);
    }

    public function __destruct()
    {
        $this->unlock();
        fclose($this->resource);
    }
}
