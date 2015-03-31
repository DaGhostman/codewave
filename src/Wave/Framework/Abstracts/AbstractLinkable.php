<?php
/**
 * Created by PhpStorm.
 * User: elham_asmar
 * Date: 31/03/2015
 * Time: 16:25
 */

namespace Wave\Framework\Abstracts;


use Wave\Framework\Adapters\Link\Linkable;
use Wave\Framework\Common\Link;

abstract class AbstractLinkable implements Linkable {

    /**
     * @type object
     */
    protected $instance;

    /**
     * @type array
     */
    protected $links = [];

    /**
     * @param $instance object
     * @throws \InvalidArgumentException
     */
    public function __construct($instance)
    {
        if (!is_object($instance)) {
            throw new \InvalidArgumentException(
                sprintf('Expected argument of type object, "%s" received', gettype($instance))
            );
        }

        $this->instance = $instance;
    }

    /**
     * Pushes a Link to the pool
     *
     * @param \Wave\Framework\Common\Link $link
     * @return $this
     */
    public function addLink (Link $link)
    {
        array_push($this->links, $link);

        return $this;
    }

    /**
     * Returns cloned instance of the wrapped class
     *
     * @return mixed
     */
    public function getState()
    {
        return clone $this->instance;
    }

    /**
     * Simple implementation of the notification process,
     * note that this method is meant to cover only simple cases
     * and is advisable to overwrite it.
     */
    public function notify ()
    {
        $this->instance = $this->getState();
        foreach ($this->links as $link) {
            $link->update($this)
                ->notify();
        }
    }

    /**
     * Handles the main process, it must proxy all method calls to the
     * instance and trigger the `notify` method when the state of
     * the instance changes.
     * It should allow returning of the method call results, but must
     * not return bare instance of the linked object.
     *
     * @param $name
     * @param array $args
     * @return mixed
     */
    abstract public function __call($name, array $args = []);


    /**
     * The usual setter, but instead of storing the values
     * locally, it passes them to the wrapped instance.
     *
     * This method also triggers the update
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->instance->$name = $value;
        $this->notify();
    }
}