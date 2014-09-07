<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 04/09/14
 * Time: 22:09
 */

namespace Tests\Event;


use Wave\Framework\Event\Event;

class EventTest extends \PHPUnit_Framework_TestCase {
    protected $event;

    protected function setUp()
    {
        $scope = new \stdClass();

        $this->event = new Event('test', $scope);
        $this->event->setData(array('test' => 'data', 'bool' => true));
    }

    public function testEventScope()
    {
        $this->assertInstanceOf('\stdClass', $this->event->scope());
    }

    public function testCall()
    {
        $this->assertNull($this->event->callMeMaybe());
    }

    public function testGetters()
    {
        $this->assertSame('data', $this->event->test);
        $this->assertNull($this->event->wieredKey);
        $this->assertTrue($this->event->bool);
    }
}
