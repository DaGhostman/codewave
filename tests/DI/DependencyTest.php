<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 28/09/14
 * Time: 20:10
 */

namespace Tests\DI;

use Wave\Framework\DI\Dependency;

class Resolution
{
    public function __construct() {echo 'Success';}
    public function run() {echo 'Run';}
    public function arguments($foo, $bar, $baz) {echo print_r(array($foo, $bar, $baz), true);}
}

class DependencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Wave\Framework\DI\Dependency $dependency
     */
    private $dependency = null;

    protected function setUp()
    {
        $this->dependency = new Dependency(new Resolution());
    }

    public function testDependencyWithConstructor()
    {
        $this->dependency->setMethod('__construct');
        $dep = $this->dependency;
        $dep();
        $this->expectOutputString('Success');
    }

    public function testDependencyWithAnotherMethod()
    {
        $this->dependency->setMethod('run');
        $dep = $this->dependency;

        $dep();

        $this->expectOutputString('Run');
    }

    public function testDependencyWithoutMethod()
    {
        $this->expectOutputString('Success');
        $dep = $this->dependency;
        $this->assertInstanceOf('\Tests\DI\Resolution', $dep());
    }

    public function testDependencyWithArguments()
    {
        $this->dependency->setMethod('arguments');
        $this->dependency->addArguments(array('foo' =>'foo', 'bar' => 'bar', 'baz' => 'baz'));

        $dep = $this->dependency;
        $dep();

        $this->expectOutputString(print_r(array('foo', 'bar', 'baz'), true));
    }

    /**
     * @expectedException \LogicException
     * @exceptionMessage Too few arguments passed
     */
    public function testLogicException()
    {
        $dep = $this->dependency;
        $dep->addArgument('asd')
            ->setMethod('arguments');

        $dep();
    }

    public function testDependencyHasMethod()
    {
        $this->assertFalse($this->dependency->hasMethod());
    }

}
