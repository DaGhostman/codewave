<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 18:21
 */

namespace Tests\Decorators;


use Wave\Framework\Decorator\Decorators\BaseDecorator;
use Wave\Framework\Decorator\Decorators\Serialize;

class PlainStub extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();

        return $args[0];
    }
}

class SerializeTest extends \PHPUnit_Framework_TestCase
{
    protected $decorator = null;
    public function setUp()
    {
        $this->decorator = new Serialize();
    }

    public function testCall()
    {
        $this->assertSame(
            serialize('Hello, world!'),
            $this->decorator->call('Hello, world!')
        );
    }

    public function testCallWithNext()
    {
        $this->decorator->setNext(new PlainStub());
        $this->assertSame(
            serialize('Hello, world!'),
            $this->decorator->call('Hello, world!')
        );
    }
}
