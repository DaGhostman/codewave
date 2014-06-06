<?php
use \Wave\Pattern\Observer\Observer;
use \Wave\Pattern\Observer\Subject;

class Obser extends Observer
{

    private $output;

    public function newState()
    {
        $this->output = true;
    }

    public function getOutput()
    {
        return $this->output;
    }
}

class ObserverTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->subj = new Subject();
    }

    public function testCreation()
    {
        $this->assertInstanceOf('\Wave\Pattern\Observer\Observer', new Observer($this->subj));
    }

    public function testStates()
    {
        $this->assertInstanceOf('\Wave\Pattern\Observer\Subject', $this->subj->state('newState'));
        $this->assertEquals('newState', $this->subj->state());
    }

    public function testNotify()
    {
        $obser = new Obser($this->subj);
        $this->subj->state('newState');
        $this->subj->notify();
        
        $this->assertTrue($obser->getOutput());
    }

    public function testObserverExistence()
    {
        $this->obser = new Obser($this->subj);
        $this->assertEquals(0, $this->subj->hasObserver($this->obser));
    }

    public function testManualAttahcmentDetachment()
    {
        $this->obser = new Obser($this->subj);
        $this->assertInstanceOf('\Wave\Pattern\Observer\Subject', $this->subj->detach($this->obser));
        $this->assertFalse($this->subj->hasObserver($this->obser));
    }

    /**
     * @expectedException \ErrorException
     */
    public function testExceptions()
    {
        $o = new Subject();
        $a = new Subject();
        $dummy = new Observer($o);
        $dummy2 = new Obser($a);
        $o->detach($dummy2);
    }
}
?>