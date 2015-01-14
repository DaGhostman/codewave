<?php
/**
 * Created by PhpStorm.
 * User: dagho_000
 * Date: 13/01/2015
 * Time: 18:21
 */

namespace Wave\Framework\Decorator;


class Decorator
{
    protected $decorators = array();
    protected $callback = null;

    public function __construct($callback) {}
    public function addDecorator($decorator) {}

    public function commit()
    {
        $result = call_user_func_array($this->callback, func_get_args());
        for ($i=0; $i<count($this->decorators); $i++) {
            $result = call_user_func(array($this->decorators[$i], 'commit'), $result);
        }

        return $result;
    }
    public function rollback()
    {
        $result = null;
        for ($i=count($this->decorators)-1; $i>-1; $i--) {
            $result = call_user_func(array($this->decorators[$i], 'rollback'), func_get_arg(0));
        }

        return $result;
    }

}