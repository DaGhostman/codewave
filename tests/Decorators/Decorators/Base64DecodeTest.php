<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 18:09
 */

namespace Tests\Decorators;


use Wave\Framework\Decorator\Decorators\Base64Decode;
use Wave\Framework\Decorator\Decorators\BaseDecorator;

class PlainDecorator extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();

        return $args[0];
    }
}

class Base64DecodeTest extends \PHPUnit_Framework_TestCase
{
    protected $based = 'SGVsbG8sIHdvcmxkIQ==';
    /**
     * @var $decorator Base64Decode
     */
    protected $decorator;
    protected function setUp()
    {
        $this->decorator = new Base64Decode();
    }
    public function testCall()
    {
        $this->assertSame(
            'Hello, world!',
            $this->decorator->call($this->based)
        );

        $this->assertFalse($this->decorator->hasNext());
        $this->assertNull($this->decorator->next());
    }

    public function testCallWithNext()
    {
        $this->decorator->setNext(new DecoratorStub());
        $this->assertSame(
            'Hello, world!',
            $this->decorator->call($this->based)
        );
    }
}
