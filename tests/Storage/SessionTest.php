<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 16:55
 */

namespace Tests\Storage;

use Wave\Framework\Decorator\BaseDecorator;
use Wave\Framework\Storage\Session;

if (!function_exists('\Tests\Storage\setcookie')) {
    function setcookie($name, $val)
    {
        return null;
    }
}


class SessionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $_COOKIE['x_session_id'] = serialize('i_am_unique_id');
    }

    public function testSessionInit()
    {
        $session = new Session();
        $this->assertSame(serialize('i_am_unique_id'), $session->getId());
        $this->assertSame('i_am_unique_id', $session->x_session_id);
        $session->num = 3;
    }
}
