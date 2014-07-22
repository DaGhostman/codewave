<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 21/07/14
 * Time: 17:38
 */

namespace Tests\View\Extensions\HTML\Components;


use Wave\View\Extensions\HTML\Components\Head;

class HeadTest extends \PHPUnit_Framework_TestCase
{

    protected $head;

    protected function setUp()
    {
        $this->head = new Head(
            'Test',
            array(array('key' => 'value')),
            array(array('rel' => 'stylesheet')),
            array(array('type' => 'text/javascript')),
            array('dummy' => array('class' => 'small'))
        );
    }

    public function testHeadCreation()
    {
        $this->expectOutputString(
            '<head>'.
            '<meta key="value">'.
            '<link rel="stylesheet">'.
            '<script type="text/javascript"></script>'.
            '<dummy class="small"></dummy>'.
            '<title>Test</title>'.
            '</head>'
        );

        echo $this->head;
    }

    public function testHelpers()
    {
        $this->expectOutputString(
            '<head>'.
            '<meta key="value">'.
            '<meta key="value">'.
            '<link rel="stylesheet">'.
            '<link rel="stylesheet">'.
            '<script type="text/javascript"></script>'.
            '<script type="text/javascript"></script>'.
            '<dummy class="small"></dummy>'.
            '<foo>Bar</foo>'.
            '<title>Test</title>'.
            '</head>'
        );

        $this->head->addMeta(array('key' => 'value'));
        $this->head->addLink(array('rel' => 'stylesheet'));
        $this->head->addScript(array('type' => 'text/javascript'));
        $this->head->addCustom('foo', array('content' => 'Bar'));
        echo $this->head;
    }

}
