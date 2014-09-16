<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 22:59
 */

namespace Tests;

use Wave\Framework\Decorator\Decoratable;
use Wave\Framework\Decorator\Decorators\BaseDecorator;

class PlainDecorator extends BaseDecorator
{
    public function call()
    {
        echo 'Plain';
        if ($this->hasNext()) {
            $this->next()->call();
        }
    }
}

class ChainDecorator extends BaseDecorator
{

    public function call()
    {
        echo 'Chain';
        if ($this->hasNext()) {
            $this->next()->call();
        }
    }
}

class DecoratableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var $decoratable Decoratable
     */
    protected $decoratable;
    protected function setUp()
    {
        $this->decoratable = new Decoratable();
    }

    public function testDecoratorInvokeWODecorators()
    {
        $this->assertTrue($this->decoratable->invokeCommitDecorators(true));
        $this->assertTrue($this->decoratable->invokeRollbackDecorators(true));

        $this->assertSame(array(1, 0), $this->decoratable->invokeCommitDecorators(array(1,0)));
        $this->assertSame(array(1, 0), $this->decoratable->invokeRollbackDecorators(array(1,0)));
    }

    public function testDecoratorDefinition()
    {
        $this->expectOutputString('PlainPlain');

        $this->decoratable->addCommitDecorator(new PlainDecorator());
        $this->decoratable->addRollbackDecorator(new PlainDecorator());
        $this->decoratable->invokeCommitDecorators();
        $this->decoratable->invokeRollbackDecorators();
    }

    public function testDecoratorChains()
    {
        $this->expectOutputString('PlainChainPlainChain');
        
        $dec = new PlainDecorator();
        $dec->setNext(new ChainDecorator());

        $this->decoratable->addCommitDecorator($dec);
        $this->decoratable->addRollbackDecorator($dec);

        $this->decoratable->invokeCommitDecorators();
        $this->decoratable->invokeRollbackDecorators();
    }

    public function testChainDecoratorsFromArray()
    {
        $this->expectOutputString('ChainPlainChainPlain');

        
        $decoratorsCommit = array(
            new PlainDecorator(),
            new ChainDecorator()
        );
        $decoratorsRollback = array(
            new PlainDecorator(),
            new ChainDecorator()
        );

        $this->decoratable->chainCommitDecorators($decoratorsCommit);
        $this->decoratable->chainRollbackDecorators($decoratorsRollback);

        $this->decoratable->invokeCommitDecorators();
        $this->decoratable->invokeRollbackDecorators();
    }

    public function testDecoratorChainOneToOne()
    {
        $this->expectOutputString('ChainPlainChainPlain');

        
        $this->decoratable->addCommitDecorator(new PlainDecorator());
        $this->decoratable->addRollbackDecorator(new PlainDecorator());
        $this->decoratable->chainCommitDecorator(new ChainDecorator());
        $this->decoratable->chainRollbackDecorator(new ChainDecorator());

        $this->decoratable->invokeCommitDecorators();
        $this->decoratable->invokeRollbackDecorators();
    }

    public function testDecoratorChainOneToMany()
    {
        $this->expectOutputString('ChainPlainChainChainPlainChain');

        
        $plain = new PlainDecorator();
        $chain = new ChainDecorator();
        $plain->setNext($chain);

        $this->decoratable->addCommitDecorator($plain);
        $this->decoratable->addRollbackDecorator($plain);

        $this->decoratable->chainCommitDecorator(new ChainDecorator());
        $this->decoratable->chainRollbackDecorator(new ChainDecorator());

        $this->decoratable->invokeCommitDecorators();
        $this->decoratable->invokeRollbackDecorators();
    }

    /**
     * @expectedException \LogicException
     */
    public function testCommitLogicExceptionOnChaining()
    {
        
        $commit = new PlainDecorator();
        $commit->setNext(new ChainDecorator());

        $this->decoratable->addCommitDecorator(new PlainDecorator());
        $this->decoratable->chainCommitDecorator($commit);
    }

    /**
     * @expectedException \LogicException
     */
    public function testRollbackLogicExceptionOnChaining()
    {
        

        $rollback = new PlainDecorator();
        $rollback->setNext(new ChainDecorator());

        $this->decoratable->addRollbackDecorator(new PlainDecorator());
        $this->decoratable->chainRollbackDecorator($rollback);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddBadCommitDecorator()
    {
        

        $this->decoratable->addCommitDecorator(new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddBadRollbackDecorator()
    {
        $this->decoratable->addRollbackDecorator(new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadCommitChainDecorator()
    {
        $this->decoratable->chainCommitDecorator(new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadCRollbackChainDecorator()
    {
        $this->decoratable->chainRollbackDecorator(new \stdClass());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEmptyCommitChainDecorator()
    {
        $this->decoratable->chainCommitDecorator(new PlainDecorator());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEmptyRollbackChainDecorator()
    {
        $this->decoratable->chainRollbackDecorator(new PlainDecorator());
    }
}
