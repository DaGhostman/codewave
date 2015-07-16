<?php
namespace Test\Service;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use Stub\MockUrl;
use Stub\StubRequest;
use Stub\StubResponse;
use Wave\Framework\Service\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    use HttpMockTrait;

    private $client;

    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
    }

    public static function tearDownAfterClass()
    {
        static::tearDownHttpMockAfterClass();
    }

    public function setUp()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped(
                'The MySQLi extension is not available.'
            );

            return;
        }
        $this->setUpHttpMock();

        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/api')
            ->then()
                ->statusCode(200)
                ->header('User-Agent', 'PHPUnit/TestCase')
                ->body('{}')
            ->end();

        $this->http->setUp();

        $this->client = new Client(new StubRequest('GET', new MockUrl('/api', null, 'localhost', 8082)));
    }

    public function tearDown()
    {
        $this->tearDownHttpMock();
    }

    public function testSimpleRequest()
    {
        $this->assertSame(
            '{}', $this->client->send(new StubResponse())->getBody()
        );
    }

    public function testRequestWithHeaders()
    {
        $this->client->withHeaders();

        $this->assertSame('PHPUnit/TestCase', $this->client->send(new StubResponse())->getHeader('User-Agent'));
    }

    public function testRequestWithoutHeaders()
    {

        $this->assertNull($this->client->send(new StubResponse())->getHeader('User-Agent'));
    }

    public function testCurlResource()
    {
        $this->assertTrue(is_resource(
            (new Client(new StubRequest('GET', new MockUrl('/api', null, 'localhost', 8082))))
                ->getCurl())
        );
    }
}
