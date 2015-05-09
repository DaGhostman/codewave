<?php

namespace Wave\Framework\Common\Observer;

class Observer
{
    protected $subscriptions = [];

    public function __construct(Subject $subject)
    {
        $subject->addObserver($this);
    }

    public function subscribe($event, $callback)
    {
        if (!array_key_exists($event, $this->subscriptions)) {
            $this->subscriptions[$event] = [];
        }

        array_push($this->subscriptions[$event], $callback);

        return $this;
    }

    public function trigger($event, $args = [])
    {
        if (array_key_exists($event, $this->subscriptions)) {
            foreach ($this->subscriptions[$event] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }
}
