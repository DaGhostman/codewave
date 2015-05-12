<?php
namespace Test\Http;

use Stub\MockQuery;
use Wave\Framework\Http\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    private $url;

    protected function setUp()
    {
        $this->url = new Url();
    }

    public function testConstructor()
    {
        $url = new Url('/index', null, 'example.com', 88, 'https', 'anchor');
        $this->assertSame('https', $url->getScheme());
        $this->assertSame('example.com', $url->getHost());
        $this->assertSame(88, $url->getPort());
        $this->assertSame('/index', $url->getPath());
        $this->assertNull($url->getQuery());
        $this->assertSame('anchor', $url->getFragment());

        $this->assertSame('https://example.com:88/index#anchor', (string) $url);
    }

    public function testSettersPreserveImmutability()
    {
        $url = new Url();
        $this->assertInstanceOf('\Wave\Framework\Http\Url', $url->setScheme('http'));
        $this->assertNotSame($url, $url->setHost('http'));
        $this->assertInstanceOf('\Wave\Framework\Http\Url', $url->setHost('localhost'));
        $this->assertNotSame($url, $url->setHost('localhost'));
        $this->assertInstanceOf('\Wave\Framework\Http\Url', $url->setPort(8080));
        $this->assertNotSame($url, $url->setPort(8080));
        $this->assertInstanceOf('\Wave\Framework\Http\Url', $url->setPath('/main'));
        $this->assertNotSame($url, $url->setPath('/main'));
        $this->assertInstanceOf('\Wave\Framework\Http\Url', $url->setQuery(new MockQuery()));
        $this->assertNotSame($url, $url->setQuery(new MockQuery()));
        $this->assertInstanceOf('\Wave\Framework\Http\Url', $url->setFragment('anchor'));
        $this->assertNotSame($url, $url->setFragment('anchor'));
    }

    public function testFullToStringMethod()
    {
        $this->assertSame(
            'https://example.com/index?param=value#anchor',
            (string) new Url(
                '/index',
                (new MockQuery())->import(['param' => 'value']),
                'example.com',
                443,
                'https',
                'anchor'
            )
        );
    }

    public function testIsHttpsChecker()
    {
        $this->assertFalse($this->url->isHttps());
        $this->assertTrue($this->url->setScheme('https')->isHttps());
    }


    public function testConstructorSchemeException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new Url('/', null, null, 888, 'file');
    }

    public function testSetSchemeException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->url->setScheme('file');
    }

    public function testOutOfRangePortInConstructor()
    {
        $this->setExpectedException('\OutOfRangeException');
        new Url('/', null, null, 123456);
    }

    public function testOutOfRangePortInSetter()
    {
        $this->setExpectedException('\OutOfRangeException');
        $this->url->setPort(123456);
    }

    public function testInvalidPortTypeInConstructor()
    {
        $this->setExpectedException('\InvalidArgumentException');

        new Url('/', null, null, '22');
    }

    public function testInvalidPortTypeInSetter()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->url->setPort('22');
    }

    public function testSerialization()
    {
        $url = clone $this->url;
        $this->assertSame(serialize($this->url), serialize($url));
    }

    public function testDeserialization()
    {
        $url = serialize($this->url);
        $this->assertEquals($this->url, unserialize($url));
    }
}
