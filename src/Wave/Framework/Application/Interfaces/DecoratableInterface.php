<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 14:46
 */

namespace Wave\Framework\Application\Interfaces;


interface DecoratableInterface
{
    /**
     * @param $decorator
     *
     * @return mixed
     */
    public function addCommitDecorator($decorator);
    public function addRollbackDecorator($decorator);
    public function chainCommitDecorator($decorator);
    public function chainRollbackDecorator($decorator);

    /**
     * @param $decoratorChain array Indexed array of decorators
     *
     * @return $this
     */
    public function chainCommitDecorators($decoratorChain);
    public function chainRollbackDecorators($decoratorChain);

    public function invokeCommitDecorators();
    public function invokeRollbackDecorators();
}
