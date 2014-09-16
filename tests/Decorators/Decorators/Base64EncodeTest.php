<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 18:00
 */

namespace Tests\Decorators;


use Wave\Framework\Decorator\Decorators\Base64Encode;
use Wave\Framework\Decorator\Decorators\BaseDecorator;

class DecoratorStub extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();

        return $args[0];
    }
}

class Base64EncodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Base64Encode
     */
    private $decorator = null;

    public function setUp()
    {
        $this->decorator = new Base64Encode();
    }

    public function testCall()
    {
        $this->assertSame(
            base64_encode('simpleString'),
            $this->decorator->call('simpleString')
        );

        $this->assertFalse($this->decorator->hasNext());
        $this->assertNull($this->decorator->next());
    }

    public function testCallWithNext()
    {
        $this->decorator->setNext(new DecoratorStub());

        $this->assertSame(
            base64_encode('simpleString'),
            $this->decorator->call('simpleString')
        );
    }
}
