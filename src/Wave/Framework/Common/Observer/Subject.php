<?php

namespace Wave\Framework\Common\Observer;

class Subject
{
    /**
     * @var array|Observer
     */
    private $observers = [];

    /**
     * Notifies the observers that an $event has occurred.
     *
     * @param string $event
     * @param array $args
     */
    public function notify($event, $args = [])
    {
        /**
         * @var $observer Observer
         */
        foreach ($this->observers as $observer) {
            $observer->trigger($event, $args);
        }
    }

    public function addObserver(Observer $observer)
    {
        $this->observers[] = $observer;
    }
}
