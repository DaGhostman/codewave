<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 19:15
 */

namespace Tests\Decorators;

use Wave\Framework\Decorator\Decorators\BaseDecorator;
use Wave\Framework\Decorator\Decorators\Decrypt;

class StubDecorator extends BaseDecorator
{
    public function call()
    {
        $args = func_get_args();

        return $args[0];
    }
}

class DecryptTest extends \PHPUnit_Framework_TestCase
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

        $this->decorator = new Decrypt(
            $this->key,
            $this->key
        );
    }

    public function testCall()
    {
        $this->assertSame(
            'simple',
            $this->decorator->call(
                base64_decode('TwECBAP9nIvoShY4qwPTJ8QdxskH3CAkb+gSDjTDmAw=')
            )
        );
    }

    public function testCallWithNext()
    {
        $this->decorator->setNext(new StubDecorator());

        $this->assertSame(
            'simple',
            $this->decorator->call(
                base64_decode('TwECBAP9nIvoShY4qwPTJ8QdxskH3CAkb+gSDjTDmAw=')
            )
        );
    }
}
