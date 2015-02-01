<?php
/**
 * Created by PhpStorm.
 * User: dagho_000
 * Date: 12/01/2015
 * Time: 15:03
 */

namespace Wave\Framework\Event;


class Emitter
{
    protected $events = array();

    protected static $instance = null;

    protected function __construct()
    {

    }
    protected function __clone()
    {
        throw new \RuntimeException("Object is not meant to be cloned");
    }

    /**
     * @return $this
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Emitter();
        }

        return self::$instance;
    }

    public function on($event, $callback)
    {
        if (!array_key_exists($event, $this->events)) {
            $this->events[$event] = array();
        }

        array_push($this->events[$event], $callback);

        return $this;
    }

    public function trigger($name, $data = array(), $custom = array())
    {
        if (!array_key_exists($name, $this->events)) {
            return null;
        }

        foreach ($this->events[$name] as $e) {
            call_user_func($e, $data, $custom);
        }
    }
}
