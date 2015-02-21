<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 01/02/15
 * Time: 15:50
 */
namespace Test\Decorator\Decorators;

use Wave\Framework\Decorator\Decorators\Base64;

class Base64Test extends \PHPUnit_Framework_TestCase
{

    private $decorator = null;

    private $stub = null;

    protected function setUp()
    {
        $this->stub = base64_encode('dummy');
        $this->decorator = new Base64();
    }

    public function testCommit()
    {
        $this->assertSame(base64_encode('dummy'), $this->decorator->commit('dummy'));
    }

    public function testRollback()
    {
        $this->assertSame(base64_decode($this->stub), $this->decorator->rollback($this->stub));
    }
}
