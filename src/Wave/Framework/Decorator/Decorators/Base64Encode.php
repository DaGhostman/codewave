<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 17:33
 */

namespace Wave\Framework\Decorator\Decorators;


class Base64Encode extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();

        if ($this->hasNext()) {
            return $this->next()->call(base64_encode($args[0]));
        }

        return base64_encode($args[0]);
    }
}
