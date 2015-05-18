<?php

namespace Wave\Framework\Common\Observer;

/**
 * Class Subject
 *
 * @package Wave\Framework\Common\Observer
 */
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
     * @param array $context
     */
    public function notify($event, array $context = [])
    {
        /**
         * @var $observer Observer
         */
        foreach ($this->observers as $observer) {
            $observer->trigger($event, $context);
        }
    }

    /**
     * @param \Wave\Framework\Common\Observer\Observer $observer
     */
    public function addObserver(Observer $observer)
    {
        $this->observers[] = $observer;
    }
}
