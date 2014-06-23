<?php

namespace Tests\View;

use Wave\View\Engine;

class EngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorException()
    {
        new \Wave\View\Engine('../dummy_path');
    }

    public function testFileExtensionChange()
    {
        $eng = new \Wave\View\Engine('../');
        $this->assertInstanceOf(
            '\Wave\View\Engine',
            $eng->setFileExtension('phtml')
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadRegister()
    {
        $eng = new \Wave\View\Engine('../');
        $eng->register('dummyTemplate', 'none/existing/folder');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadRenderCall()
    {
        $eng = new Engine('../');
        $eng->render("dummy::index");
    }

    public function testRendering()
    {
        if (!is_dir('tests/small/ro')) {
            mkdir('tests/small/ro', 0755, true);
        }

        $engine = new \Wave\View\Engine('tests/small/');
        $engine->register("test", '/ro/');

        $engine->name = "Small and simple";
        if (!file_exists('tests/small/ro/index.phtml')) {
            file_put_contents(
                'tests/small/ro/index.phtml',
                '<p>Hello, <?=$this->name?></p>'
            );
        }


        $this->expectOutputString("<p>Hello, Small and simple</p>");

        print $engine->render('test::index');

        unlink('tests/small/ro/index.phtml');
    }

    public function testExtensionRendering()
    {
        $engine = new \Wave\View\Engine('tests/small/');
        $engine->register("test", '/ro/');
        $engine->loadExtension('upper', function ($str) {
            return strtoupper($str);
        });

        if (!file_exists('tests/small/ro/ext.phtml')) {
            file_put_contents(
                'tests/small/ro/ext.phtml',
                '<p>Hello, <?=$this->upper($this->name)?></p>'
            );
        }
        $engine->name = "Small and simple";

        $this->expectOutputString("<p>Hello, SMALL AND SIMPLE</p>");

        print $engine->render('test::ext');

        unlink('tests/small/ro/ext.phtml');
    }

    public function __destruct()
    {
        if (is_dir('tests/small/ro')) {
            rmdir('tests/small/ro');
        }
        if (is_dir('tests/small')) {
            rmdir('tests/small');
        }

    }
}
