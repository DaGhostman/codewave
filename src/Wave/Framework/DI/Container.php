<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 26/09/14
 * Time: 13:33
 */

namespace Wave\Framework\DI;


class Container
{

    /**
     * @var \ReflectionClass
     */
    protected $reflection = null;

    protected $container = array();

    public function push($alias, $callback)
    {
        $this->container[$alias] = $callback;
    }

    /**
     * @param $alias
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function get($alias)
    {
        if (!array_key_exists($alias, $this->container)) {
            throw new \RuntimeException(
                sprintf('Alias %s does not exist', $alias)
            );
        }

        return call_user_func($this->container[$alias]);
    }

    public function resolve($class, $method = null)
    {
        if (is_string($class) && !class_exists($class, true)) {
            throw new \LogicException(
                sprintf('Unable to resolve class %s it does not exist', $class)
            );
        }
        $this->reflection = new \ReflectionClass($class);

        $dependency = new Dependency($class);
        if (is_null($method)) {
            if ($this->reflection->getConstructor()) {
                $parser = new Parser($this, $this->reflection->getConstructor()->getDocComment());
                $dependency->setMethod('__construct');
            }
        } elseif (!is_null($method)) {
            $parser = new Parser($this, $this->reflection->getMethod($method)->getDocComment());
            $parser->setContainer($this);
            $dependency->setMethod($method);
        }

        $dependency->addArguments((isset($parser) ? $parser->getDependencies() : array()));

        return $dependency();
    }
}
