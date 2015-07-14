<?php


namespace Test\Http;


use Stub\MockUrl;
use Stub\StubRequest;
use Wave\Framework\Http\Entities\Parameters\Json;

class JsonParametersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Json
     */
    protected $params;
    protected function setUp()
    {
        $this->params = new Json(new StubRequest('GET', new MockUrl()));
    }

    public function testGetters()
    {
        $this->assertSame('value', $this->params->fetch('key'));
        $this->assertSame('value', $this->params['key']);
        $this->assertTrue($this->params->has('key'));
        $this->assertFalse(isset($this->params['bad-key']));
    }

    public function testInvalidKeyRequest()
    {
        $this->setExpectedException('\Wave\Framework\Exceptions\InvalidKeyException');
        $this->params->fetch('bad-key');
    }

    public function testInvalidKeyOnArrayAccess()
    {
        $this->setExpectedException('\Wave\Framework\Exceptions\InvalidKeyException');
        $this->params['bad-key'];
    }

    public function testSetters()
    {
        $this->params->set('param', 'value');
        $this->params['param2'] = true;
        $this->assertTrue($this->params->has('param'));
    }

    public function testRemovals()
    {
        $this->params->remove('key');
        $this->assertFalse($this->params->has('key'));
    }

    public function testRemovalsInArrayAccess()
    {
        unset($this->params['key']);
        $this->assertFalse($this->params->has('key'));
    }

    public function testExceptionInRemovals()
    {
        $this->setExpectedException('\Wave\Framework\Exceptions\InvalidKeyException');
        unset($this->params['bad-key']);
    }

    public function testToStringConversion()
    {
        $this->assertSame(json_encode(['key' => 'value']), (string) $this->params);
    }
}
