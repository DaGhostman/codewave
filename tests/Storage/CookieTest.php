<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 16:37
 */

namespace Tests\Storage {

    $_COOKIE = array('test' => true);
    use Wave\Framework\Storage\Cookie;

    if (!function_exists('\Tests\Storage\setcookie')) {
        function setcookie()
        {
            return null;
        }
    }


    class CookieTest extends \PHPUnit_Framework_TestCase
    {
        public function testCookieExistingCreation()
        {
            $cookie = new Cookie('test');

            $this->assertTrue($cookie->get());
        }

        public function testCookieCreation()
        {
            $cookie = new Cookie('simple');
            $this->assertNull($cookie->get());
            $this->assertFalse($cookie->exists());

        }

        public function testCookieGetter() {
            $cookie = new Cookie('test');
            $cookie->set('false');

            $this->assertSame('false', $cookie->get());
            $this->assertTrue($cookie->exists());
            $this->assertSame('false', (string) $cookie);

        }
    }
}
