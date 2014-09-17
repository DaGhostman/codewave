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
        $result = array_shift($args);

        if ($this->hasNext()) {
            $result = $this->next()->call($result);
        }

        return base64_encode($result);
    }
}
