<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 28/09/14
 * Time: 23:58
 */

namespace Tests\DI;


use Wave\Framework\DI\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Wave\Framework\DI\Parser
     */
    private $parser = null;

    protected function setUp()
    {

        $doc = <<<HEREDOC
/**
 * @inject \Bar\Baz \$baz
 */
HEREDOC;


        $mock = $mock = \Mockery::mock('Wave\Framework\DI\Container');
        $mock->shouldReceive('resolve')
            ->withAnyArgs()
            ->andReturnUsing(function ($string) {
                return $string;
            });


        $this->parser = new Parser($mock, $doc);
    }

    public function testDependencies()
    {
        $this->assertSame(
            array('baz' => '\Bar\Baz'),
            $this->parser->getDependencies()
        );
    }
}
