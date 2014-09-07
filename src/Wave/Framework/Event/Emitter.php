<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/08/14
 * Time: 23:24
 */

namespace Wave\Framework\Event;


use Wave\Framework\Storage\Registry;

class Emitter
{

    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    /**
     * @var Registry
     */
    protected static $storage;


    public static function setUp()
    {

        if (!self::$storage instanceof Registry) {
            self::$storage = new Registry(array(
                'mutable' => true,
                'replace' => true
            ));

            return false;
        }

        return true;
    }



    /**
     * @param string $event Creates event
     */
    public static function defineEvent($event)
    {
        self::setUp();
        self::$storage->set($event, new \SplPriorityQueue());
    }

    /**
     * @param string $event Event name to look for
     *
     * @return bool
     */
    public static function hasEvent($event)
    {
        self::setUp();
        return isset(self::$storage[$event]);
    }

    /**
     * Binds a callback to an event. Has the ability to prioritize
     * the callbacks.
     *
     * @param string $event Event name to bind on
     * @param callable $callback The callback to register
     * @param int $priority Priority of the callback
     *
     * @return Emitter
     */
    public static function on($event, $callback, $priority = 1)
    {
        self::setUp();

        if (!self::$storage->exists($event)) {
            self::defineEvent($event);
        }

        self::$storage->get($event)
            ->insert($callback, $priority);
    }

    /**
     * Dispatches an event and constructs an context for it.
     *
     * @param string $event Event name
     * @param array $data Data to pass to the event handlers
     * @param object $scope The object which instantiates the event
     *
     * @throws \OutOfBoundsException
     */
    public static function trigger($event, $data = null, $scope = null)
    {
        self::setUp();
        if (!self::$storage->exists($event)) {
            throw new \OutOfBoundsException(
                sprintf("Trying to trigger non-existing event '%s'.", $event)
            );
        }

        $queue = self::$storage->get($event);
        $queue->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
        $queue->top();


        while ($queue->valid()) {
            $e = new Event(
                $event,
                (!is_null($scope) ? $scope : new \stdClass())
            );

            call_user_func(
                $queue->current(),
                $e->setData($data)
            );

            $queue->next();
        }
    }

    /**
     * Clears the list of events and all listeners
     *
     * @return $this
     */
    public static function resetEvents()
    {
        self::$storage = new Registry(array(
            'mutable' => true,
            'replace' => true
        ));
    }

    /**
     * Returns all of the event listeners for a given event
     *
     * @param $event string The event for which to return the listeners
     *
     * @return mixed
     */
    public static function getListeners($event)
    {
        return self::$storage->get($event);
    }
}
