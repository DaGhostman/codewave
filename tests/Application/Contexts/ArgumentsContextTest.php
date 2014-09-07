<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 27/08/14
 * Time: 19:45
 */

namespace Tests\Application\Contexts;


use Wave\Framework\Application\Contexts\ArgumentsContext;
use Wave\Framework\Application\Interfaces\ContextInterface;

class StubContext implements ContextInterface {
    protected $context, $scope;
    public function __construct($scope) {$this->scope = $scope;}
    public function scope (){ return $this->scope; }
    public function push ($key, $value) { $this->context[$key] = $value; }
    public function fetch ($key) { return $this->context[$key];}
}

class ArgumentsContextTest extends \PHPUnit_Framework_TestCase {
    public function testCreation()
    {
        $context = new ArgumentsContext(new \stdClass());
        $this->assertInstanceOf(
            '\Wave\Framework\Application\Contexts\ArgumentsContext',
            $context
        );

        $context->push('exists', true);

        $this->assertTrue($context->fetch('exists'));

        $this->assertInstanceOf('\stdClass', $context->scope());

    }

    public function testIteration()
    {
        $context = new ArgumentsContext(new \stdClass());

        $this->assertInstanceOf('\Traversable', $context->getIterator());
    }

    public function testValueResolving()
    {
        $context = new ArgumentsContext(new \stdClass(), array(
            'string' => 'Hello world',
            'bool_true' => 'true',
            'bool_false' => 'false',
            'int' => '123',
            'float' => '1234.52',
            'quoted' => '"Quoted String"'
        ));

        $this->assertEquals('Hello world', $context->fetch('string'));
        $this->assertEquals('Hello world', $context->get('string'));
        $this->assertEquals('Hello world', $context->string);
        $this->assertEquals('Hello world', $context->fetch('string'));
        $this->assertTrue(is_bool($context->fetch('bool_true')));
        $this->assertTrue($context->fetch('bool_true'));
        $this->assertTrue(is_bool($context->fetch('bool_false')));
        $this->assertFalse($context->fetch('bool_false'));
        $this->assertTrue(is_numeric($context->fetch('int')));
        $this->assertTrue(is_int($context->fetch('int')));
        $this->assertEquals(123, $context->fetch('int'));
        $this->assertTrue(is_numeric($context->fetch('float')));
        $this->assertTrue(is_float($context->fetch('float')));
        $this->assertTrue(is_double($context->fetch('float')));
        $this->assertSame('Quoted String', $context->fetch('quoted'));

        $this->assertNull($context->fetch('notThere'));
    }

    public function testScope()
    {
        $context = new ArgumentsContext(new \stdClass());

        $this->assertInstanceOf('\stdClass', $context->scope());
    }
}
