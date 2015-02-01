<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 01/02/15
 * Time: 15:27
 */

namespace Stub\Decorator;

class SerializeDecoratorStub
{
    public function commit($data)
    {
        return serialize($data);
    }

    public function rollback($data)
    {
        return unserialize($data);
    }
}
