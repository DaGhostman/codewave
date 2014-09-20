<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 14:32
 */

namespace Wave\Framework\Decorator\Decorators;


abstract class BaseDecorator
{
    /**
     * @var BaseDecorator
     */
    protected $next = null;

    /**
     * @return mixed
     * @codeCoverageIgnore
     */
    abstract public function call();

    /**
     * @return BaseDecorator
     */
    public function next()
    {
        if (!is_null($this->next)) {
            return $this->next;
        }
    }

    /**
     * @param $next BaseDecorator
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setNext($next)
    {
        if (!is_null($next) && !$next instanceof BaseDecorator) {
            throw new \InvalidArgumentException(
                "Argument passed must be instance of BaseDecorator"
            );
        }

        $this->next = $next;

        return $this;
    }

    /**
     * Check if current decorator has another decorator chained to it
     *
     * @return bool
     */
    public function hasNext()
    {
        return !is_null($this->next);
    }
}
