<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 18:37
 */

namespace Tests\Decorators;


use Wave\Framework\Decorator\Decorators\BaseDecorator;
use Wave\Framework\Decorator\Decorators\Encrypt;

class StubDec extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();

        return $args[0];
    }
}

class EncryptTest extends \PHPUnit_Framework_TestCase
{
    protected $decorator;
    protected $key;

    protected function setUp()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped(
                'The MCrypt extension is not available.'
            );
        }

        $this->key = base64_decode('Wsxwx8Eq2P0Uh7+7GlqGu2jN4TcoGnisUnZy89zDFsc=');
        $this->decorator = new Encrypt(
            $this->key,
            $this->key
        );
    }

    public function testCall()
    {
        $this->assertSame(
            'TwECBAP9nIvoShY4qwPTJ8QdxskH3CAkb+gSDjTDmAw=',
            base64_encode($this->decorator->call('simple'))
        );
    }

    public function testCallWithNext()
    {
        $this->decorator->setNext(new StubDec());

        $this->assertSame(
            'TwECBAP9nIvoShY4qwPTJ8QdxskH3CAkb+gSDjTDmAw=',
            base64_encode($this->decorator->call('simple'))
        );
    }
}
