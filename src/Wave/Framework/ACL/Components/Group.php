<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 18/09/14
 * Time: 00:00
 */

namespace Wave\Framework\ACL\Components;

use Wave\Framework\ACL\Helpers\PermissionsHelper;

class Group extends PermissionsHelper
{
    protected $permissions = array();
    protected $path = null;



    public function isAllowed($id)
    {
        return in_array($id, $this->permissions);
    }

    public function isDenied($id)
    {
        return !in_array($id, $this->permissions);
    }
}
