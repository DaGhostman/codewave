<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 18:32
 */

namespace Tests\Decorators;

use Wave\Framework\Decorator\Decorators\BaseDecorator;
use Wave\Framework\Decorator\Decorators\Unserialize;

class StubPlain extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();

        return $args[0];
    }
}

class UnserializeTest extends \PHPUnit_Framework_TestCase
{
    protected $decorator = null;
    public function setUp()
    {
        $this->decorator = new Unserialize();
    }

    public function testCall()
    {
        $this->assertSame(
            'Hello, world!',
            $this->decorator->call(serialize('Hello, world!'))
        );
    }

    public function testCallWithNext()
    {
        $this->decorator->setNext(new StubPlain());
        $this->assertSame(
            'Hello, world!',
            $this->decorator->call(serialize('Hello, world!'))
        );
    }
}
