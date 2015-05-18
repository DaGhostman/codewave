<?php

namespace Wave\Framework\Common\Observer;

class Observer
{
    protected $subscriptions = [];

    /**
     * @param \Wave\Framework\Common\Observer\Subject $subject
     */
    public function __construct(Subject $subject = null)
    {
        if (null !== $subject) {
            $subject->addObserver($this);
        }
    }

    /**
     * @param string $event
     * @param callable $callback
     *
     * @return $this
     */
    public function subscribe($event, callable $callback)
    {
        if (!array_key_exists($event, $this->subscriptions)) {
            $this->subscriptions[$event] = [];
        }

        $this->subscriptions[$event][] = $callback;

        return $this;
    }

    /**
     * @param string $event
     * @param array $context
     */
    public function trigger($event, array $context = [])
    {
        if (array_key_exists($event, $this->subscriptions)) {
            foreach ($this->subscriptions[$event] as $callback) {
                call_user_func_array($callback, $context);
            }
        }
    }
}
