<?php


namespace Test\Application;

use Wave\Framework\Application\ApplicationFactory;

class ApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApplicationFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mock;

    public function testSettingRequestClassException()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'The request class needs to implement \Wave\Framework\Interfaces\Http\RequestInterface'
        );

        $this->factory->setRequest('\stdClass')
            ->build($this->mock);
    }

    public function testSettingResponseClassException()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'The response class needs to implement \Wave\Framework\Interfaces\Http\ResponseInterface'
        );

        $this->factory->setResponse('\stdClass')
            ->build($this->mock);
    }

    public function testSettingServerClassException()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'The server class needs to implement \Wave\Framework\Interfaces\Http\ServerInterface'
        );

        $this->assertNull($this->factory->setServer('\stdClass')
            ->build($this->mock));
    }

    protected function setUp()
    {
        $this->mock = $this->getMock(
            '\Wave\Framework\Http\Url', null, [], '', true
        );
        $this->factory = new ApplicationFactory([
            'CONTENT_TYPE' => 'text/html',
            'REQUEST_METHOD' => 'GET'
        ]);
    }
}
