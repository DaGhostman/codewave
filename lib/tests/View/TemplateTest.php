<?php

namespace Tests\View;


use Wave\View\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    private $template;
    protected function setUp()
    {
        mkdir('tests/ro', 0755, true);
        touch('tests/ro/index.phtml');
        file_put_contents('tests/ro/index.php', 'hey');
        $this->template = new Template('index', 'tests/ro', 'phtml');
    }

    protected function tearDown()
    {
        unlink('tests/ro/index.phtml');
        if (is_dir('tests/ro')) {
            rmdir('tests/ro');
        }
    }

    public function testSingleAssign()
    {
        $this->assertSame($this->template, $this->template->assign('key', 'value'));
        $this->assertEquals('value', $this->template->key);
    }

    public function testMultipleAssign()
    {
        $this->assertSame(
            $this->template,
            $this->template->assignAll(array('key' => 'value', 'key1' => 'value1'))
        );

        $this->assertSame('value', $this->template->key);
        $this->assertSame('value1', $this->template->key1);
    }

    public function testExtensions()
    {
        $this->template->addExtension('test', function () {
            return true;
        });

        $this->assertTrue($this->template->test());
    }

    public function testRendering()
    {
        $this->expectOutputString('hey');
        print $this->template;
    }
}
