<?php
namespace Wave\Framework\Common;

use Wave\Framework\Adapters\Link\Destination;
use Wave\Framework\Adapters\Link\Linkable;

class Link
{
    /**
     * @type object
     */
    private $destination = null;


    private $links = [];

    /**
     * Sets the destination of the link
     *
     * @param \Wave\Framework\Adapters\Link\Destination $destination
     */
    public function __construct(Destination $destination)
    {
        $this->destination = $destination;
    }

    /**
     * Notifies the destination of linkable change
     */
    public function update($instance)
    {
        $class = get_class($instance);

        if (!array_key_exists($class, $this->links)) {
            throw new \InvalidArgumentException(
                sprintf('Class \'%s\' not found in link', $class)
            );
        }

        $this->links[$class][0] = $instance;

        return $this;
    }

    public function notify()
    {
        foreach ($this->links as $key => $value) {
            call_user_func([$this->destination, $value[1]], $value[0]);
        }
    }

    /**
     * Injects a link
     *
     * @param \Wave\Framework\Adapters\Link\Linkable $link
     * @param $via string Method of the destination to invoke for the update
     *
     * @return $this
     */
    public function push(Linkable $link, $via)
    {
        if (!method_exists($this->destination, $via)) {
            throw new \InvalidArgumentException(
                sprintf('Method \'%s\' does not exists in class \'%s\'', $via, get_class($this->destination))
            );
        }

        $link->addLink($this);
        $this->links[get_class($link)] = [$link, $via];

        return $this;
    }
}
