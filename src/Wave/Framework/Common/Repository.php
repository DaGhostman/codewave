<?php
namespace Wave\Framework\Common;

/**
 * Class Repository
 * @package Wave\Framework\Common
 *
 * This class should server as a *very simple* DI container,
 * which can contain application logic which needs to be reused
 * across models, controller and/or other user-land objects.
 */
class Repository implements \ArrayAccess, \Countable
{
    /**
     * @var Repository
     */
    protected static $instance;

    protected $storage = [];

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
            $this->storage[$name] = $callback;
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
        return new Repository([]);
    }

    /**
     * Instantiates the current object
     *
     * @param array $bootstrap Array to pass to the constructor
     * @return Repository
     */
    public static function getInstance(array $bootstrap = [])
    {
        if (!self::$instance instanceof Repository) {
            self::$instance = new Repository($bootstrap);
        }

        return self::$instance;
    }

    public function singleton($name, callable $callback)
    {
        if (array_key_exists($name, $this->static)) {
            throw new \RuntimeException(
                sprintf('Unable to override %s')
            );
        }

        $this->storage[$name] = call_user_func($callback);
    }

    public function bind($name, callable $callback)
    {
        if (array_key_exists($name, $this->storage)) {
            throw new \InvalidArgumentException(
                sprintf('Name %s already registered', $name)
            );
        }

        $this->storage[$name] = $callback;
    }

    public function remove($name)
    {
        if (!array_key_exists($name, $this->storage[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Cannot unset not existing declaration %s', $name)
            );
        }

        unset($this->storage[$name]);
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
    public function invoke($name, $args = [])
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
            $this->bind($name, $value);
        }
    }

    public function offsetGet($name)
    {
        if ($this->offsetExists($name)) {
            if (is_callable($this->storage[$name])) {
                return $this->invoke($name, []);
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
        $this->remove($name);
    }

    public static function destroy()
    {
        unset(self::$instance);
    }
}
