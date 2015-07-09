<?php
namespace Wave\Framework\Common;

use \Wave\Framework\Exceptions\DuplicateKeyException;
use Wave\Framework\Exceptions\InvalidKeyException;

/**
 * Class Repository should server as a *very simple* DI container,
 * which can contain application logic which needs to be reused
 * across models, controller and/or other user-land objects.
 *
 * @package Wave\Framework\Common
 *
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
     *
     * @throws \LogicException
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
        if (array_key_exists($name, $this->storage)) {
            throw new DuplicateKeyException(
                sprintf('Name %s already defined', $name)
            );
        }

        $this->storage[$name] = call_user_func($callback);

        return true;
    }

    public function bind($name, callable $callback)
    {
        if (array_key_exists($name, $this->storage)) {
            throw new DuplicateKeyException(
                sprintf('Name %s already defined', $name)
            );
        }

        $this->storage[$name] = $callback;
    }

    public function remove($name)
    {
        if (!array_key_exists($name, $this->storage)) {
            throw new InvalidKeyException(
                sprintf('Cannot unset non existing declaration %s', $name)
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
    public function invoke($name, array $args = [])
    {
        if (array_key_exists($name, $this->storage)) {
            if ($this->storage[$name] instanceof \Closure || is_callable($this->storage[$name])) {
                return call_user_func_array($this->storage[$name], $args);
            }

            return $this->storage[$name];
        }

        throw new \LogicException(
            sprintf('Trying to access non-declared entry %s', $name)
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

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->storage);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $name
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @throws DuplicateKeyException
     * @internal param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     */
    public function offsetSet($name, $value)
    {
        if (is_callable($value)) {
            $this->bind($name, $value);
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $name
     * @throws \RuntimeException
     * @throws \LogicException
     * @return mixed Can return all value types.
     * @internal param mixed $offset The offset to retrieve.
     *
     */
    public function offsetGet($name)
    {
        return $this->invoke($name);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $name
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @internal param mixed $offset <p>
     * An offset to check for.
     * </p>
     */
    public function offsetExists($name)
    {
        if (self::$instance) {
            return array_key_exists($name, $this->storage);
        }

        return false;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $name
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($name)
    {
        $this->remove($name);
    }

    public function destroy()
    {
        $this->storage = [];
        self::$instance = null;
    }
}
