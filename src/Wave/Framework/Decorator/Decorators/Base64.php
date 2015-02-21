<?php
/**
 * Created by PhpStorm.
 * User: dagho_000
 * Date: 14/01/2015
 * Time: 02:48
 */
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
