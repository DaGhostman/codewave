<?php
namespace Test\Helper\Controller;

use Wave\Framework\Helper\Controller\MultiContentController;

class MultiContentControllerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->controller = new \Stub\MultiContentController();
    }

    public function testIndexCallForJson()
    {
        $this->expectOutputString('"Hello, World!"');
        $this->controller->index(null, ['contentType' => 'json']);
    }

    public function testIndexCallForXml()
    {
        $this->expectOutputString('<message>Hello, World!</message>');
        $this->controller->index(null, ['contentType' => 'xml']);
    }

    public function testUnexpectedTypeCall()
    {
        $this->setExpectedException('\RuntimeException');
        $this->controller->index(null, ['contentType' => 'html']);
    }

    public function testNoHandlerFound()
    {
        $this->setExpectedException('\LogicException');
        $this->controller->badMethod(null, ['contentType' => 'xml']);
    }
}
