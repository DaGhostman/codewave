<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 30/07/14
 * Time: 22:00
 */

namespace Tests\Di;

use Wave\Di\Container;

class DependencyStub
{
    public function getInteger()
    {
        return 5;
    }
}

class ResolveStub
{
    /**
     * @inject \Tests\Di\DependencyStub
     */
    public function getInteger($arg)
    {
        return $arg->getInteger();
    }
}

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    protected $container = null;
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testDefinition()
    {
        $this->container->register('stub', function () {
            return new DependencyStub();
        });

        $this->assertSame(5, $this->container->get('stub')->getInteger());
    }

    public function testDefinitionOverride()
    {
        $this->container->register('stub', function () {
            return new DependencyStub();
        }, true);

        $this->assertFalse($this->container->register('stub', function () {
            return 'Should not get registered';
        }));
    }

    public function testGet()
    {
        $this->container->register('stub', function () {
            return new DependencyStub();
        });

        $this->assertInstanceOf('\Wave\Di\Dependency', $this->container->resolve('\Tests\Di\ResolveStub'));
        $this->assertSame(5, $this->container->resolve('\Tests\Di\ResolveStub')->getInteger());
        $this->assertInstanceOf('\Wave\Di\Dependency', $this->container->get('stub'));
        $this->assertNull($this->container->get('non_existing'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testResolveException()
    {
        $this->container->resolve('some_unreflectable_string');
    }
}
