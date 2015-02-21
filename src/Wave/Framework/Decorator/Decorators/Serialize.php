<?php
/**
 * Created by PhpStorm.
 * User: dagho_000
 * Date: 14/01/2015
 * Time: 02:49
 */
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
