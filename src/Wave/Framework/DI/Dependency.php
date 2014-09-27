<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 25/09/14
 * Time: 23:07
 */

namespace Wave\Framework\DI;


class Dependency
{

    protected $method;
    protected $class;

    protected $arguments = array();
    /**
     * @param $name string Class name
     */
    public function __construct($name)
    {
        $this->class = $name;
    }

    /**
     * @param $arguments array
     * @return $this
     */
    public function addArguments($arguments)
    {
        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }

    /**
     * @param $argument mixed
     *
     * @return $this
     */
    public function addArgument($argument)
    {
        array_push($this->arguments, $argument);

        return $this;
    }

    public function setMethod($methodName)
    {
        $this->method = $methodName;
    }

    public function hasMethod()
    {
        return (bool) $this->method;
    }

    public function __invoke()
    {
        $ref = new \ReflectionClass($this->class);

        $depends = array();
        if (is_null($this->method) || !$ref->hasMethod($this->method)) {
            return $ref->newInstance();
        }
        $method = $ref->getMethod($this->method);

        if ($method->getNumberOfParameters() > count($this->arguments)) {
            throw new \LogicException('Too few parameters passed');
        }

        foreach ($method->getParameters() as $param) {
            $depends[$param->getPosition()] = $this->arguments[$param->getName()];
        }

        if ($this->method == '__construct') {
            return $ref->newInstanceArgs($depends);
        } else {
            return call_user_func_array(array($this->class, $this->method), $depends);
        }
    }
}
