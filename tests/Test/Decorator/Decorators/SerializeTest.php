<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 01/02/15
 * Time: 15:56
 */

namespace Test\Decorator\Decorators;


use Wave\Framework\Decorator\Decorators\Serialize;

class SerializeTest extends \PHPUnit_Framework_TestCase
{
    private $decorator = null;
    private $stub = null;
    protected function setUp()
    {
        $this->stub = serialize([1, 2, 3]);
        $this->decorator = new Serialize();
    }

    public function testCommit()
    {
        $this->assertSame($this->stub, $this->decorator->commit([1, 2, 3]));
    }

    public function testRollback()
    {
        $this->assertSame(unserialize($this->stub), $this->decorator->rollback($this->stub));
    }
}
