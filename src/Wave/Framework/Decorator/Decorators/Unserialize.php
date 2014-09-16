<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 16:01
 */

namespace Wave\Framework\Decorator\Decorators;


class Unserialize extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();
        if ($this->hasNext()) {
            return $this->next()->call(unserialize($args[0]));
        }

        return unserialize($args[0]);
    }
}
