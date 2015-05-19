<?php


namespace Test\Http;


use Wave\Framework\Http\Entities\Url\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Query
     */
    protected $query;

    protected function setUp()
    {
        $this->query = new Query(['arg' => 'value']);
    }

    public function testNewQueryConstructor()
    {
        $q = new Query('arg=value&arg1=value2');
        $this->assertSame('value', $q->get('arg'));
    }

    public function testGetter()
    {
        $this->assertTrue($this->query->has('arg'));
        $this->assertSame('value', $this->query->get('arg'));
        $this->assertFalse($this->query->has('bad-key'));
    }

    public function testSetters()
    {
        $q = $this->query->set('argument', 'value');
        $this->assertNotSame(spl_object_hash($this->query), spl_object_hash($q));
        $this->assertTrue($q->has('argument'));
        $this->assertFalse($this->query->has('argument'));
        $this->assertSame('value', $q->get('argument'));
    }

    public function testInvalidGetKeyException()
    {
        $this->setExpectedException('\Wave\Framework\Exceptions\InvalidKeyException');
        $this->query->get('bad-key');
    }

    public function testInvalidRemoveKeyException()
    {
        $this->setExpectedException('\Wave\Framework\Exceptions\InvalidKeyException');
        $this->query->remove('bad-key');
    }

    public function testRemovalOfObjects()
    {
        $this->assertNull($this->query->remove('arg'));
        $this->assertFalse($this->query->has('arg'));
    }

    public function testArrayImports()
    {
        $q = $this->query->import(['argc' => 0, 'argv' => '']);
        $this->assertNotSame($this->query, $q);
        $this->assertTrue($q->has('argc'));
        $this->assertTrue($q->has('argv'));

        $this->assertFalse($this->query->has('argc'));
    }

    public function testCountAndStringCast()
    {
        $this->assertSame(1, count($this->query));
        $this->assertSame('arg=value', (string) $this->query);
    }
}
