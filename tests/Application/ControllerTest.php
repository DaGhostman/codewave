<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 25/08/14
 * Time: 16:44
 */

namespace Tests\Application;


use Wave\Framework\Application\Controller;

class ControllerInstanceStub {
    public function action() {return null;}
}

class ControllerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \Wave\Framework\Application\Controller
     */
    private $controller = null;

    protected function setUp()
    {
        $this->controller = new Controller();
    }

    public function testControllerMethods()
    {
        $this->controller->via('GET');
        $this->assertTrue($this->controller->supportsHTTP('get'));
    }

    public function testControllerConditions()
    {
        $this->controller->setPattern('/:name');
        $this->controller->conditions(array('name' => 'Jo'));
        $this->assertTrue($this->controller->match('/Jo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid callback specified
     */
    public function testControllerActionInvalid()
    {
        $this->controller->action('nonExistingFunction');
    }

    public function testControllerSerialize()
    {
        $this->controller->action(array('\Tests\Application\ControllerInstanceStub', 'action'));
        $this->controller->setPattern('/');
        $this->controller->via('GET');

        $ctrl = unserialize(serialize($this->controller));

        $this->assertEquals($this->controller, $ctrl);

        $this->controller->action(function() {return true;});

        $this->assertSame('N;', serialize($this->controller));
    }
}
