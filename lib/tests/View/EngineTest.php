<?php

namespace Tests\View;

use Wave\View\Engine;
use Wave\View\Parser\General;

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
                '<!DOCTYPE html><html><body><p>Hello, <?php echo $this->name; ?></p></body></html>'
            );
        }


        $this->expectOutputString("<!DOCTYPE html>".PHP_EOL."<html><body><p>Hello, Small and simple</p></body></html>");

        print $engine->render('test::index');

        unlink('tests/small/ro/index.phtml');
    }

    public function testExtensionMagicCall()
    {
        $engine = new \Wave\View\Engine('./');
        $obj = new \stdClass();
        $obj->existing = true;
        $engine->loadExtension('test', $obj);

        $this->assertTrue($engine->ext('test')->existing);
        $this->assertNull($engine->ext('non_exist'));
    }

    public function testExtensionRendering()
    {
        $engine = new \Wave\View\Engine('tests/small/');
        $engine->register("test", '/ro/');
        $engine->setParser(new General());
        $engine->loadExtension('upper', function ($str) {
            return strtoupper($str);
        });
        $engine->loadExtension('date', function () {
            return '2014';
        });

        if (!file_exists('tests/small/ro/ext.phtml')) {
            file_put_contents(
                'tests/small/ro/ext.phtml',
                '<!DOCTYPE html><p>Hello, <?php echo $this->upper($this->name); ?>!<br>Date implemented: <!-- extension:date ( format="Y" ) --></p>'
            );
        }
        $engine->name = "Small and simple";

        $this->expectOutputString('<!DOCTYPE html>'.PHP_EOL.'<html><body><p>Hello, SMALL AND SIMPLE!<br>Date implemented: 2014</p></body></html>');

        print $engine->render('test::ext');

        unlink('tests/small/ro/ext.phtml');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParserSetter()
    {
        $engine = new \Wave\View\Engine('tests/small/');
        $engine->setParser('string');
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
