<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 21/07/14
 * Time: 19:02
 */

namespace Tests\View\Extensions\HTML\Components;


use Wave\View\Extensions\HTML\Components\Script;

class ScriptTest extends \PHPUnit_Framework_TestCase
{
    protected $inline;
    protected $remote;
    protected $doc;

    protected function setUp()
    {
        $this->doc = new \DOMDocument();
        $this->inline = new Script($this->doc, array(
            'type' => 'text/javascript',
            'script' => 'alert("Test");'
        ));

        $this->remote = new Script($this->doc, array(
            'type' => 'text/javascript',
            'src' => '/js/script.js'
        ));
    }

    public function testInlineGeneration()
    {
        $this->expectOutputString(
            '<script type="text/javascript">alert("Test");</script>' . PHP_EOL
        );

        $this->doc->appendChild($this->inline->getScript());
        echo $this->doc->saveHTML();
    }

    public function testRemoteGeneration()
    {
        $this->expectOutputString(
            '<script type="text/javascript" src="/js/script.js"></script>' . PHP_EOL
        );

        $this->doc->appendChild($this->remote->getScript());
        echo $this->doc->saveHTML();
    }
}
