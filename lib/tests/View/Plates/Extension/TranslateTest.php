<?php

use \Wave\View\Plates\Extension\Translate;

class TranslateTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var \Wave\View\Plates\Extension\Translate Instance for testing
     */
    protected $testable, $locales;
    
    protected function setUp()
    {
        $translation = array(
            "foo" => "Bar",
            "lorem Ipsum" => "Ipsum Lorem"
        );
        
        $this->locales = array('en_GB', 'en_US', 'bg_BG');
        
        $this->testable = new Translate(
            $translation,
            'en_GB',
            $this->locales
        );
    }
    
    public function testCurrentLocale()
    {
        $this->assertEquals($this->testable->locale(), 'en_GB');
    }
    
    public function testSingleKeyNeedle()
    {
        $this->assertEquals($this->testable->translate('foo'), 'Bar');
    }
    
    public function testMultiWordNeedle()
    {
        $this->assertEquals(
            $this->testable->translate('lorem Ipsum'),
            'Ipsum Lorem'
        );
    }
    
    public function testNonExistingNeedle()
    {
        $this->assertEquals($this->testable->translate("PHP"), 'PHP');
    }
    
    public function testLocales()
    {
        $this->assertEquals(
            $this->testable->availableLocales(),
            array('en_GB', 'en_US', 'bg_BG')
        );
    }
    
    public function testEmptyObject()
    {
        $testable = new Translate(array());
        $this->assertEquals($testable->translate("foo"), 'foo');
        $this->assertEquals($testable->locale(), 'all_ALL');
        $this->assertEquals($testable->availableLocales(), array());
    }
    
    public function testFunctions()
    {
        $expected = array(
            'translate' => 'translate',
            'trans'     => 'translate',
            't'         => 'translate',
            '__'        => 'translate',
            'locales'   => 'availableLocales'
        );
        
        $this->assertEquals($this->testable->getFunctions(), $expected);
    }
}
