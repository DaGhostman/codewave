<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 30/07/14
 * Time: 22:00
 */

namespace Tests\Application;

use Wave\Framework\Application\IoC;

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
     * @inject \Tests\Application\DependencyStub $arg
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
        $this->container = new IoC();
    }

    public function testDefinition()
    {
        $this->container->register('stub', function () {
            return new DependencyStub();
        });

        $this->assertSame(5, $this->container->get('stub')->getInteger());
    }

    public function testGet()
    {
        $this->container->register('stub', function () {
            return new DependencyStub();
        });


        $this->assertInstanceOf(
            '\Tests\Application\ResolveStub',
            $this->container->resolve('\Tests\Application\ResolveStub')
        );
        $this->assertSame(5, $this->container->resolve(
            $this->container->resolve('\Tests\Application\ResolveStub'),
            'getInteger'
        ));
        $this->assertInstanceOf('\Tests\Application\DependencyStub', $this->container->get('stub'));

    }

    public function testStringResolve()
    {
        $this->assertInstanceOf('\stdClass', $this->container->resolve('\stdClass'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testResolveException()
    {
        $this->container->resolve('some_unreflectable_string');
    }
}
