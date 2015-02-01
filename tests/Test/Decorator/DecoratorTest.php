<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 01/02/15
 * Time: 15:30
 */

namespace Test\Decorator;


use Stub\Decorator\SerializeDecoratorStub;
use Wave\Framework\Decorator\Decorator;

class DecoratorTest extends \PHPUnit_Framework_TestCase
{
    private $decorator;
    protected function setUp()
    {
        $this->decorator = new Decorator(function () {
            return [1,2,3];
        });
        $this->decorator->addDecorator(new SerializeDecoratorStub);
    }

    public function testCreation()
    {
        $decorator = new Decorator(function () {
            return 0;
        });

        $this->assertEquals(1, $decorator->addDecorator(new SerializeDecoratorStub()));
    }

    public function testCommit()
    {
        $this->assertSame(serialize([1, 2, 3]), $this->decorator->commit());
    }

    public function testRollback()
    {
        $this->assertSame([1, 2, 3], $this->decorator->rollback(serialize([1, 2, 3])));
    }
}
