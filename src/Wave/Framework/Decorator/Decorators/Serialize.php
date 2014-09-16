<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 15:58
 */

namespace Wave\Framework\Decorator\Decorators;


class Serialize extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();
        if ($this->hasNext()) {
            return $this->next()->call(serialize($args[0]));
        }

        return serialize($args[0]);
    }
}
