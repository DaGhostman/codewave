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

    protected $decorators = [];

    protected $callback = null;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function addDecorator($decorator)
    {
        return array_push($this->decorators, $decorator);
    }

    public function commit()
    {
        $result = call_user_func_array($this->callback, func_get_args());
        for ($i = 0; $i < count($this->decorators); $i ++) {
            $result = call_user_func([
                $this->decorators[$i],
                'commit'
            ], $result);
        }
        
        return $result;
    }

    public function rollback()
    {
        $result = null;
        for ($i = count($this->decorators) - 1; $i > - 1; $i --) {
            $result = call_user_func([
                $this->decorators[$i],
                'rollback'
            ], func_get_arg(0));
        }
        
        return $result;
    }
}
