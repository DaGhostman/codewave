<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 20/09/14
 * Time: 22:49
 */

namespace Tests\Storage;

use Wave\Framework\Storage\Flash;

class SessionStub implements \ArrayAccess {
    protected $messages = array(
        'flash-error' => array('Error'),
        'flash-warning' => array('Warning'),
        'flash-notice' => array('Notice'),
        'flash-info' => array('Info'),
        'flash-shared' => array('Shared')
    );

    public function offsetSet($key, $value) {}
    public function offsetExists($key) {}
    public function offsetUnset($key) {}
    public function offsetGet($key) {
        return $this->messages[$key];
    }
}

class FlashTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Wave\Framework\Storage\Flash
     */
    private $flash = null;
    protected function setUp()
    {
        $this->flash = new Flash(new SessionStub());
    }

    public function testConstruct()
    {
        $flash = new Flash(new SessionStub());

        $this->assertSame('Error', $flash->getError());
    }

    public function testGetters()
    {
        $this->assertSame('Error', $this->flash->getError());
        $this->assertSame('Warning', $this->flash->getWarning());
        $this->assertSame('Notice', $this->flash->getNotice());
        $this->assertSame('Info', $this->flash->getInfo());
        $this->assertSame('Shared', $this->flash->getShared());
    }
}
