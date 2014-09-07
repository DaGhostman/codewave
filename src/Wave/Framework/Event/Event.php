<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/08/14
 * Time: 23:25
 */

namespace Wave\Framework\Event;


use Wave\Framework\Event\Contexts\EventContext;

class Event
{
    /**
     * @var string Event name
     */
    protected $name = null;

    /**
     * @var Contexts\EventContext
     */
    protected $context = null;

    public function __construct($name, $scope)
    {
        $this->name = $name;
        $this->context = new EventContext($scope);
        $this->mutable = false;
        $this->replace = null;
    }

    public function setData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $entry) {
                $this->context->push($key, $entry);
            }
        }

        return $this;
    }

    public function __call($name, $args)
    {
        if ($name == 'scope') {
            return $this->context->scope();
        }

        return null;
    }

    public function __get($key)
    {
        return $this->context->fetch($key);
    }
}
