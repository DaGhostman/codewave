<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 29/09/14
 * Time: 12:47
 */

namespace Tests\DI;


use Wave\Framework\DI\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Wave\Framework\DI\Container $container
     */
    private $container = null;

    protected function setUp()
    {
        $this->container = new Container();
        $this->container->push('foo', function() { return '\Foo'; });
    }

    public function testGetter()
    {
        $this->assertSame('\Foo', $this->container->get('foo'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetterException()
    {
        $this->container->get('non-existing');
    }
}
