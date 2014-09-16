<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 22:52
 */

namespace Wave\Framework\Decorator;


use Wave\Framework\Application\Interfaces\DecoratableInterface;
use Wave\Framework\Decorator\Decorators\BaseDecorator;

class Decoratable implements DecoratableInterface
{

    protected $commitDecorator = null;
    protected $rollbackDecorator = null;

    /**
     * @param $decorator BaseDecorator
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function addCommitDecorator($decorator)
    {
        if (!$decorator instanceof BaseDecorator) {
            throw new \InvalidArgumentException(
                "Argument should be instance of BaseDecorator"
            );
        }

        $this->commitDecorator = $decorator;

        return $this;
    }

    /**
     * @param $decorator BaseDecorator
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function addRollbackDecorator($decorator)
    {
        if (!$decorator instanceof BaseDecorator) {
            throw new \InvalidArgumentException(
                "Argument should be instance of BaseDecorator"
            );
        }

        $this->rollbackDecorator = $decorator;

        return $this;
    }

    /**
     * @param $decorator BaseDecorator
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException If $decorator has a chain
     */
    public function chainRollbackDecorator($decorator)
    {

        if (!$decorator instanceof BaseDecorator) {
            throw new \InvalidArgumentException(
                'Supplied argument must be instance of BaseDecorator'
            );
        }
        if (!$this->rollbackDecorator instanceof BaseDecorator) {
            throw new \RuntimeException(
                'Unable to chain decorator. No decorator has been defined'
            );
        }

        if ($decorator->hasNext()) {
            throw new \LogicException(
                'Attempting to overwrite decorators chain'
            );
        }

        $this->rollbackDecorator = $decorator->setNext($this->rollbackDecorator);
    }

    /**
     * @param $decorator BaseDecorator
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException If $decorator has a chain
     */
    public function chainCommitDecorator($decorator)
    {
        if (!$decorator instanceof BaseDecorator) {
            throw new \InvalidArgumentException(
                'Supplied argument must be instance of BaseDecorator'
            );
        }

        if (!$this->commitDecorator instanceof BaseDecorator) {
            throw new \RuntimeException(
                'Unable to chain decorator. No decorator has been defined'
            );
        }

        if ($decorator->hasNext()) {
            throw new \LogicException(
                'Attempting to overwrite decorators chain.'
            );
        }

        $this->commitDecorator = $decorator->setNext($this->commitDecorator);
    }

    /**
     * @param $decoratorChain array Indexed array of decorators
     *
     * @return $this
     */
    public function chainCommitDecorators($decoratorChain)
    {
        foreach ($decoratorChain as $node) {
            if (is_null($this->commitDecorator)) {
                $this->addCommitDecorator($node);
                continue;
            }
            $this->chainCommitDecorator($node);
        }
    }

    /**
     * @param $decoratorChain array Indexed array of decorators
     *
     * @return $this
     */
    public function chainRollbackDecorators($decoratorChain)
    {
        foreach ($decoratorChain as $node) {
            if (is_null($this->rollbackDecorator)) {
                $this->addRollbackDecorator($node);
                continue;
            }

            $this->chainRollbackDecorator($node);
        }
    }

    /**
     * @return mixed The result of the end decorator
     */
    public function invokeCommitDecorators()
    {
        $args = func_get_args();
        if ($this->commitDecorator instanceof BaseDecorator) {
            return call_user_func_array(array($this->commitDecorator, 'call'), $args);
        }

        if (func_num_args() == 1) {
            return $args[0];
        }

        return $args;
    }

    /**
     * @return mixed The result of the end decorator
     */
    public function invokeRollbackDecorators()
    {
        $args = func_get_args();
        if ($this->rollbackDecorator instanceof BaseDecorator) {
            return call_user_func_array(array($this->rollbackDecorator, 'call'), $args);
        }

        if (func_num_args() == 1) {
            return $args[0];
        }

        return $args;
    }
}
