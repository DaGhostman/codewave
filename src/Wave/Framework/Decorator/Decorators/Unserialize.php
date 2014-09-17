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
        $result = array_shift($args);

        if ($this->hasNext()) {
            $result = $this->next()->call($result);
        }

        return unserialize($result);
    }
}
