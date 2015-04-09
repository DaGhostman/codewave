<?php

namespace Test\Common;


use Wave\Framework\Common\Link;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    protected $destination;
    protected $linkable;

    protected function setUp()
    {
        $this->destination = $this->getMock(
            '\Wave\Framework\Adapters\Link\Destination',
            ['test']
        );

        $this->destination->expects($this->any())
            ->method('test')
            ->will($this->returnCallback(function() {
                echo gettype(func_get_arg(0)) . ' received';
            }));

        $this->linkable = $this->getMock('\Wave\Framework\Adapters\Link\Linkable');
    }

    public function testConstruction()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid destination provided');
        $link = new Link(new \stdClass());
    }

    public function testBadUpdate()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Class \'stdClass\' not found in link');

        $link = new Link($this->destination);
        $link->update(new \stdClass());
    }

    public function testUpdateAndNotify()
    {
        $this->expectOutputString('object received');

        $link = new Link($this->destination);
        $link->push($this->linkable, 'test');
    }
}
