<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 07/09/14
 * Time: 00:11
 */

namespace Tests\Event;


use Wave\Framework\Event\Emitter;

class EmitterTest extends \PHPUnit_Framework_TestCase {
    public function testSetUp()
    {
        $this->assertFalse(Emitter::setUp());
        $this->assertTrue(Emitter::setUp());
    }

    public function testEventCreation()
    {
        $this->assertFalse(Emitter::setUp());
        Emitter::defineEvent('test');
        $this->assertTrue(Emitter::hasEvent('test'));
    }

    public function testEventCall()
    {
        $this->expectOutputString('Event Called');

        $this->assertFalse(Emitter::setUp());
        Emitter::on('test', function() {print "Event Called";});
        Emitter::trigger('test');
    }

    public function testEventTriggerWithArguments()
    {
        $this->expectOutputString("Got Bar");

        $this->assertFalse(Emitter::setUp());
        Emitter::on('test', function($e) {echo sprintf("Got %s", $e->foo);});
        Emitter::trigger('test', array('foo' => 'Bar'));
    }

    public function testScopeInstance()
    {
        $this->expectOutputString('stdClass');
        $this->assertFalse(Emitter::setUp());
        Emitter::on('test', function($e) {echo get_class($e->scope());});
        Emitter::trigger('test', null, new \stdClass());
    }

    public function testEventResetting()
    {
        $this->assertFalse(Emitter::setUp());
        Emitter::on('test', function(){});

        $this->assertSame(1, count(Emitter::getListeners('test')));
        Emitter::resetEvents();
        $this->assertNull(Emitter::getListeners('test'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testEmitterException()
    {
        Emitter::trigger('noneExistingEvent');
    }
}
