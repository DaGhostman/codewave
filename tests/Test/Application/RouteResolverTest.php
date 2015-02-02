<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 01/02/15
 * Time: 14:35
 */

namespace Test\Application;


use Stub\Application\RouteResolverContainerStub;
use Wave\Framework\Application\RouteResolver;

class RouteResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $resolver = null;

    protected function setUp()
    {
        $this->resolver = new RouteResolver([
            'TestClass' => new \stdClass
        ]);
    }

    protected function tearDown()
    {
        $this->resolver = null;
    }

    public function testInstanceCreation()
    {
        $this->assertNotNull($this->resolver);
    }

    public function testContainerContents()
    {
        $this->assertEquals(
            ['TestClass' => new \stdClass()],
            $this->resolver->getContainer()
        );
    }

    public function testRetrievalFromContainer()
    {
        $this->assertEquals([new \stdClass(), 'method'], $this->resolver->resolve(['TestClass', 'method']));
    }
}
