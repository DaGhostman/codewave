<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 04/09/14
 * Time: 22:17
 */

namespace Tests\Event\Context;


use Wave\Framework\Event\Contexts\EventContext;

class EventContextTest extends \PHPUnit_Framework_TestCase {
    protected $eventContext;

    protected function setUp()
    {
        $this->eventContext = new EventContext(new \stdClass());
        $this->eventContext->push('test', 'data');
    }

    public function testScope()
    {
        $this->assertInstanceOf('\stdClass', $this->eventContext->scope());
    }

    public function testGetter()
    {
        $this->assertSame('data', $this->eventContext->fetch('test'));

    }
}
