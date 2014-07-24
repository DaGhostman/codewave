<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 23/07/14
 * Time: 21:31
 */

namespace Tests\View\Parser;

use Wave\View\Parser\General;

class StubExtension_Layout
{
    public function __invoke($args)
    {
        return $args[0].':'.$args[1];
    }
}

class StubFilter_SpecialChars
{
    public function __invoke($args)
    {
        return htmlspecialchars($args);
    }
}

class GeneralTest extends \PHPUnit_Framework_TestCase
{
    public function testExtensions()
    {
        $parser = new General();
        $parser->register('ext', 'layout', new StubExtension_Layout());
        $this->assertEquals('default:head', $parser->callExtension('layout', array('default', 'head')));
        $this->assertFalse($parser->callExtension('payout', array('default', 'head')));
    }

    public function testFilter()
    {
        $parser = new General();
        $parser->register('filter', 'schar', new StubFilter_SpecialChars());
        $this->assertEquals(
            htmlspecialchars('<p>Hello, World</p>'),
            $parser->callFilter('schar', '<p>Hello, World</p>')
        );
    }

    public function testUnexistentCalls()
    {
        $parser = new General();
        //$this->assertFalse($parser->call);
    }


    public function testParser()
    {
        $parser = new General();
        $parser->register('extension', 'upper', function ($string) {
            return strtoupper($string['string']);
        });

        $parser->register('filter', 'schar', function ($string) {
            return htmlspecialchars($string['string']);
        });

        $parser->register('extension', 'json', function ($array) {
            return json_encode($array['array']);
        });

        $doc = '
            <!-- extension:upper ( string="lower case string" ) -->
            <!-- filter:schar ( string="<span>SPAN TAG</span>" ) -->
            <!-- validate:pattern () -->
            <!-- extension:json(array={"version": 1.0, "php": "rlz"}) -->
        ';
        $expected = '
            LOWER CASE STRING
            &lt;span&gt;SPAN TAG&lt;/span&gt;
            <!-- validate:pattern () -->
            {"version":1,"php":"rlz"}
        ';

        $this->assertEquals($expected, $parser->parse($doc));
    }

    public function testJSONParsing()
    {
        $doc = '
            <!-- extension:json(json={"version": 1.0, "php": "rlz"}) -->
        ';

        $parser = new General();
        $parser->register('extension', 'json', function ($data) {
            $json = $data['json'];

            if (array_key_exists('version', $json) && array_key_exists('php', $json)) {
                return true;
            }
        });

        $parser->parse($doc);
    }
}
