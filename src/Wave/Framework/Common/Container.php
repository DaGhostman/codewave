<?php
namespace Wave\Framework\Common;

/**
 * Class Container
 * @package Wave\Framework\Common
 *
 * This class should server as a *very simple* DI container,
 * which can contain application logic which needs to be reused
 * across models, controller and/or other user-land objects.
 */
class Container implements \ArrayAccess, \Countable
{
    /**
     * @var Container
     */
    protected static $instance;

    protected $storage = [];
    protected $static = [];

    /**
     * Hidden constructor to disable creation of multiple instances
     * of the class, hence allowing single point of entry from all
     * classes.
     * It takes an assoc array, from which the keys are used as
     * function names and values should be the callbacks.
     *
     * @param $storage array Array to bootstrap the dependencies
     */
    protected function __construct($storage)
    {
        foreach ($storage as $name => $callback) {
            self::$instance->storage[$name] = $callback;
        }
    }

    /**
     * Prevent object cloning
     */
    private function __clone()
    {
        throw new \LogicException(
            'Class not cloneable'
        );
    }

    public static function newInstance()
    {
        return new Container([]);
    }

    /**
     * Instantiates the current object
     *
     * @param array $bootstrap Array to pass to the constructor
     * @return Container
     */
    public static function getInstance(array $bootstrap = [])
    {
        if (!self::$instance instanceof Container) {
            self::$instance = new Container($bootstrap);
        }

        return self::$instance;
    }

    public static function singleton($name, callable $callback)
    {
        if (array_key_exists($name, self::getInstance()->static)) {
            throw new \RuntimeException(
                sprintf('Unable to override %s')
            );
        }

        self::getInstance()->storage[$name] = call_user_func($callback);
    }

    public static function bind($name, callable $callback)
    {
        if (array_key_exists($name, self::getInstance()->storage)) {
            throw new \InvalidArgumentException(
                sprintf('Name %s already registered', $name)
            );
        }

        self::getInstance()->storage[$name] = $callback;
    }

    public static function remove($name)
    {
        if (!array_key_exists($name, self::getInstance()->storage[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Cannot unset not existing declaration %s', $name)
            );
        }

        unset(self::getInstance()->storage[$name]);
    }

    /**
     * Attempts to execute the registered closure or return the value of
     * the static result.
     *
     * @param $name string
     * @param $args array
     * @return mixed Result of the closure or the value of the static callback
     *
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function invoke($name, $args)
    {
        if (array_key_exists($name, $this->storage)) {
            if ($this->storage[$name] instanceof \Closure) {
                return call_user_func_array($this->storage[$name], $args);
            }

            return $this->storage[$name];
        }

        throw new \LogicException(
            sprintf('Trying to invoke non-declared method %s', $name)
        );
    }

    public static function __callStatic($name, $args)
    {
        return self::getInstance()->invoke($name, $args);
    }

    public function __call($name, $args)
    {
        return $this->invoke($name, $args);
    }

    public function count()
    {
        return count($this->storage);
    }

    public function offsetSet($name, $value)
    {
        if (is_callable($value)) {
            self::bind($name, $value);
        }
    }

    public function offsetGet($name)
    {
        if ($this->offsetExists($name)) {
            if (is_callable($this->storage[$name])) {
                return self::invoke($name, []);
            }

            return $this->storage[$name];
        }

        return null;
    }

    public function offsetExists($name)
    {
        return array_key_exists($name, $this->storage);
    }

    public function offsetUnset($name)
    {
        self::remove($name);
    }

    public static function destroy()
    {
        unset(self::$instance);
    }
}
