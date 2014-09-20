<?php

namespace Wave\Framework\ACL\Interfaces;

interface GroupAwareInterface
{
    /**
     * For convenient access, this method should call MAC::group($this)
     *
     * @return \Wave\Framework\ACL\Components\Group Returns the current object's group
     */
    public function group();
}
