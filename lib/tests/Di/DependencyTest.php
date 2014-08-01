<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 30/07/14
 * Time: 21:47
 */

namespace Tests\Di;


use Wave\Di\Dependency;

class DepStub
{
    public function getOneInteger()
    {
        return 1;
    }
}

class masterStub {
    public function with($arg) {
        return $this;
    }

    public function resolve($object, $method)
    {
        return $object->$method();
    }
}

class DependencyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDependencyCreationException()
    {
        new Dependency('bad_argument', new masterStub());
    }

    public function testInstanceFetch()
    {
        $dep = new Dependency(function () {
            return new \stdClass();
        }, new masterStub());

        $dep->present = true;
        $this->assertTrue($dep->present);
    }

    public function testDependencyCalls()
    {
        $dep = new Dependency(function () {
            return new depStub();
        }, new masterStub());

        $this->assertSame(1, $dep->getOneInteger());
    }
}
