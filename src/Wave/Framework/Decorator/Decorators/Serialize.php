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
        $result = array_shift(func_get_args());

        if ($this->hasNext()) {
            $result = $this->next()->call($result);
        }

        return serialize($result);
    }
}
