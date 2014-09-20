<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 18/09/14
 * Time: 00:00
 */

namespace Wave\Framework\ACL\Components;

use Wave\Framework\ACL\Helpers\PermissionsHelper;

class Role extends PermissionsHelper
{
    protected $permissions = array();
    protected $path = null;


    /**
     * @param $id string ID of the permission
     *
     * @return bool
     */
    public function hasAccess($id) {
        return in_array($id, $this->permissions);
    }
} 
