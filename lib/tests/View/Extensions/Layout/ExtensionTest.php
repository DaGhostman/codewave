<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 22/07/14
 * Time: 19:06
 */

namespace Tests\View\Extensions\Layout;


use Wave\View\Extensions\Layout\Extension;

class EngineStub
{
    public function render()
    {
        return 'Template';
    }
}

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLayoutRendering()
    {
        $stub = new EngineStub();
        $layout = new Extension($stub);
        $this->assertSame('Template', $layout('x'));
        $this->assertSame('Template', $stub->render());
    }

    public function testCallable()
    {
        $stub = new EngineStub();
        $layout = new Extension($stub);
        $this->assertSame('layout', $layout->getCallable());
    }
}
