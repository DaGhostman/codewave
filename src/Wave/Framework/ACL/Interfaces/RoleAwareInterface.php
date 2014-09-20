<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 17/09/14
 * Time: 23:49
 */

interface RoleAwareInterface
{
    /**
     * For convenient access, this method should call MAC::role($this)
     *
     * @return \Wave\Framework\ACL\Components\Role
     */
    public function role();
}
