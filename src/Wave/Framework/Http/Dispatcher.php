<?php
/**
 * Created by PhpStorm.
 * User: elham_asmar
 * Date: 30/03/2015
 * Time: 14:53
 */

namespace Wave\Framework\Http;

use Wave\Framework\Adapters\Link\Linkable;
use Wave\Framework\Common\Link;

class Dispatcher implements Linkable
{

    /**
     * @var $link array
     */
    protected $link = [];

    private $dispatcher = null;

    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function notify()
    {
        foreach ($this->link as $link) {
            $link->notify();
        }

        return $this;
    }

    public function update()
    {
        foreach ($this->link as $link) {
            $link->update($this);
        }

        return $this;
    }

    public function getState()
    {
        return $this->dispatcher;
    }


    public function addLink(Link $link)
    {
        $this->link[] = $link;
    }

    public function __call($name, array $args = [])
    {
        $this->dispatcher = call_user_func_array([$this->dispatcher, $name], $args);
        $this->update()
            ->notify();
    }

    public function __set($name, $value)
    {
        $this->dispatcher->$name = $value;
        $this->update()
            ->notify();
    }
}
