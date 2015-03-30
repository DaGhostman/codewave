<?php
/**
 * Created by PhpStorm.
 * User: elham_asmar
 * Date: 30/03/2015
 * Time: 12:14
 */

namespace Wave\Framework\Adapters\Link;

use \Wave\Framework\Common\Link;

interface Linkable {
    public function notify();
    public function addLink(Link $link);
    public function update();

    public function getState();
}