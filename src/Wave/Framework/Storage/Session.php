<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 12:34
 */

namespace Wave\Framework\Storage;


use Wave\Framework\Decorator\Decoratable;
use Wave\Framework\Decorator\Decorators\Serialize;
use Wave\Framework\Decorator\Decorators\Unserialize;

class Session extends Decoratable implements \ArrayAccess
{
    protected $sessionName = 'x_session_id';
    protected $uid = null;

    protected $storage = null;

    public function __construct($name = 'x_session_id')
    {
        $this->sessionName = $name;
        $cookie = new Cookie($this->sessionName);
        if (!$cookie->exists()) {
            $cookie->set(uniqid(null, true));
        }

        $this->commitDecorator = new Serialize();
        $this->rollbackDecorator = new Unserialize();

        $this->storage = new Registry(array(
            'mutable' => true,
            'replace' => true
        ));
    }

    public function getId()
    {
        $cookie = new Cookie($this->sessionName);

        if (!$cookie->exists()) {
            throw new \LogicException("Session isn't instantiated properly.");
        }
        return $this->rollbackDecorator->call($cookie->get());
    }

    public function __set($key, $value)
    {
        if ($key != $this->sessionName) {
            $this->storage->set($key, $value);
        }
    }

    public function __get($key)
    {
        $cookie = new Cookie($key);
        if (!$this->storage->exists($key) && $cookie->exists()) {
            $value = $this->invokeRollbackDecorators($cookie->get());
            $this->storage->set($key, $value);
            return $value;
        }

        return $this->storage->get($key);
    }

    public function __isset($key)
    {
        $cookie = new Cookie($key);

        return ($this->storage->exists($key) || $cookie->exists());
    }

    public function __unset($key)
    {
        $cookie = new Cookie($key);

        $this->storage->remove($key);
        $cookie->expire();
    }

    public function __destruct()
    {
        $iterator = $this->storage->getIterator();
        $iterator->rewind();
        while ($iterator->valid()) {
            $cookie = new Cookie($iterator->key());
            $cookie->set($this->invokeCommitDecorators($iterator->current()));
            $iterator->next();
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetSet($key, $value)
    {
        if ($key != $this->sessionName) {
            $this->storage->set($key, $value);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetGet($key)
    {
        $cookie = new Cookie($key);
        if (!$this->storage->exists($key) && $cookie->exists()) {
            $value = $this->invokeRollbackDecorators($cookie->get());
            $this->storage->set($key, $value);
            return $value;
        }

        return $this->storage->get($key);
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetExists($key)
    {
        return $this->storage->exists($key);
    }

    /**
     * @codeCoverageIgnore
     */
    public function offsetUnset($key)
    {
        $cookie = new Cookie($key);
        $cookie->expire();

        $this->storage->remove($key);
    }
}
