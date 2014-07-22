<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 21/07/14
 * Time: 17:23
 */

namespace Tests\View\Extensions\HTML;

use Wave\Pattern\Observer\Subject;
use Wave\View\Extensions\HTML\Extension;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $ext = null;
    protected $obj = null;
    protected function setUp()
    {
        $this->obj = new Subject();
        $this->ext = new Extension($this->obj);
    }

    public function testExtensionConstruct()
    {
        $this->assertInstanceOf('\Wave\View\Extensions\HTML\Extension', $this->ext);
        $this->assertTrue(isset($this->obj->HTML));
    }

    public function testCallable()
    {
        $this->assertSame('HTML', $this->ext->getCallable());
    }

    public function testHeadInstantiation()
    {
        $head = $this->ext->head();
        $this->assertInstanceOf('\Wave\View\Extensions\HTML\Components\Head', $head);
    }
}
