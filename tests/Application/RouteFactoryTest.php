<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 27/08/14
 * Time: 23:31
 */

namespace Tests\Application;


use Wave\Framework\Application\Factories\RouteFactory;

class ApplicationStub {
    protected $entry = null;
    public function controller($pattern, $method, $callback, $conditions = array())
    {
        $this->entry = array(
            'pattern' => $pattern,
            'methods' => $method,
            'callback' => $callback,
            'conditions' => $conditions
        );
    }

    public function getEntry()
    {
        return $this->entry;
    }
}


class RouteFactoryTest extends \PHPUnit_Framework_TestCase {
    public function testCreateSimpleRoute()
    {
        $map = '<?xml version="1.0" encoding="UTF-8"?>
        <routes>
            <route
                controller="\Tests\Application\ApplicationStub"
                method="getEntry"
                via="GET"
                pattern="/:id">
                    <conditions>
                        <condition name="id" rule="\d" />
                    </conditions>
                </route>
        </routes>
        ';

        $app = new ApplicationStub();
        $factory = new RouteFactory();

        $factory::build($map, $app);

        $entry = $app->getEntry();

        $this->assertEquals(4, count($entry));
        $this->assertSame('/:id', $entry['pattern']);
        $this->assertSame(array('GET'), $entry['methods']);
        $this->assertEquals(
            array(new \Tests\Application\ApplicationStub, 'getEntry'), $entry['callback']);

        //var_dump($entry);

        $this->assertEquals(array('id' => '\d'), (array) $entry['conditions']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Method not specified for route #
     */
    public function testInvalidCallbackMethod()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <routes>
            <route
                controller="\Stub\NoExist"
                via="GET"
                pattern="/" />
        </routes>';

        $factory = new RouteFactory();
        $app = new ApplicationStub();

        $factory::build($xml, $app);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Controller not specified for route #
     */
    public function testInvalidCallbackController()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <routes>
            <route
                method="someMethod"
                via="GET"
                pattern="/" />
        </routes>';

        $factory = new RouteFactory();
        $app = new ApplicationStub();

        $factory::build($xml, $app);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Pattern not specified for route #
     */
    public function testInvalidCallbackPattern()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <routes>
            <route
                controller="\Stub\NoExist"
                method="someMethod"
                via="GET"
                 />
        </routes>';

        $factory = new RouteFactory();
        $app = new ApplicationStub();

        $factory::build($xml, $app);
    }
}
