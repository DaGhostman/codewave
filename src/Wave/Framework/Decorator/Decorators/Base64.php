<?php
namespace Wave\Framework\Decorator\Decorators;

class Base64
{

    public function commit($val)
    {
        return base64_encode($val);
    }

    public function rollback($val)
    {
        return base64_decode($val);
    }
}
