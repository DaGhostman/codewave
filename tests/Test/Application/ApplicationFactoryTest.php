<?php


namespace Test\Application;


use Stub\MockUrl;
use Wave\Framework\Application\ApplicationFactory;

class ApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApplicationFactory
     */
    protected $factory;
    protected function setUp()
    {
        $this->factory = new ApplicationFactory([
            'CONTENT_TYPE' => 'text/html',
            'REQUEST_METHOD' => 'GET'
        ]);
    }

    public function testSettingRequestClassException()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'The request class needs to implement \Wave\Framework\Interfaces\Http\RequestInterface'
        );
        $this->factory->setRequest('\stdClass')
            ->build(new MockUrl());
    }

    public function testSettingResponseClassException()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'The response class needs to implement \Wave\Framework\Interfaces\Http\ResponseInterface'
        );

        $this->factory->setResponse('\stdClass')->build(new MockUrl());
    }

    public function testSettingServerClassException()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'The server class needs to implement \Wave\Framework\Interfaces\Http\ServerInterface'
        );

        $this->assertNull($this->factory->setServer('\stdClass')
            ->build(new MockUrl()));
    }
}
