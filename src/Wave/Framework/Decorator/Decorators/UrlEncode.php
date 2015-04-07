<?php
namespace Wave\Framework\Decorator\Decorators;

class UrlEncode
{
    public function commit($value)
    {
        return urlencode($value);
    }

    public function rollback($value)
    {
        return urldecode($value);
    }
}
