<?php
namespace Wave\Framework\Decorator\Decorators;

class Serialize
{

    public function commit($val)
    {
        return serialize($val);
    }

    public function rollback($val)
    {
        return unserialize($val);
    }
}
