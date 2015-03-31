<?php
/**
 * Created by PhpStorm.
 * User: elham_asmar
 * Date: 30/03/2015
 * Time: 14:53
 */

namespace Wave\Framework\Http;

use Wave\Framework\Abstracts\AbstractLinkable;

class Dispatcher extends AbstractLinkable
{
    public function __call($name, array $args = [])
    {
        if (method_exists($this->instance, $name)) {
            $result = call_user_func_array([$this->instance, $name], $args);
            if (substr($name, 0, 3) !== 'get') {
                $this->notify();
                $this->instance = $result;
                return $this;
            }

            return $result;
        }

        return $this;
    }
}
