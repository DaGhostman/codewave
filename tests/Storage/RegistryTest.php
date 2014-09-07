<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 14/07/14
 * Time: 18:57
 */

namespace Tests\Storage;


use Wave\Framework\Storage\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Registry
     */
    private $mutable = null;

    /**
     * @var Registry
     */
    private $immutable = null;

    /**
     * @var Registry
     */
    private $overridable = null;

    protected function setUp()
    {
        $this->mutable = new Registry(array(
            'mutable' => true,
            'replace' => false,
            'data' => array('key' => true)
        ));

        $this->immutable = new Registry(array(
            'mutable' => false,
            'data' => array('key' => true),
            'replace' => false
        ));

        $this->overridable = new Registry(array(
            'mutable' => true,
            'replace' => true,
            'data' => array('key' => true)
        ));
    }

    public function testConstructor()
    {
        $reg = new Registry(array('data' => array('key' => true)));

        $this->assertTrue($reg->get('key'));
    }

    public function testAliasProps()
    {
        $this->assertTrue($this->mutable->isMutable());
        $this->assertTrue($this->overridable->isMutable());
        $this->assertFalse($this->immutable->isMutable());

        $this->assertFalse($this->mutable->isOverridable());
        $this->assertFalse($this->immutable->isOverridable());
        $this->assertTrue($this->overridable->isOverridable());
    }

    public function testGetters()
    {
        $mutable = $this->mutable;
        $immutable = $this->immutable;
        $override = $this->overridable;

        $this->assertTrue($mutable->get('key'));

        $this->assertTrue($immutable->get('key'));

        $this->assertTrue($override->get('key'));
    }

    public function testSetters()
    {
        $override = $this->overridable;

        $override->set('key', false);

        $this->assertFalse($override->key);
    }

    public function testMutableNoOverrideSet()
    {
        $mutable = $this->mutable;
        $mutable->set('key2', false);


        $this->assertFalse($mutable->get('key2'));
        $this->assertNull($mutable->set('key', false));

        $this->assertTrue($mutable->key);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testImmutableSetter()
    {
        $immutable = $this->immutable;

        $immutable->set('key', false);
        $this->assertTrue($immutable->key);
    }

    public function testMutableRemove()
    {
        $mutable = $this->mutable;

        $mutable->remove('key');
        $this->assertNull($mutable->key);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testImmutableRemove()
    {
        $immutable = $this->immutable;

        $immutable->remove('key');
    }

    public function testExists()
    {
        $this->assertTrue($this->mutable->exists('key'));
    }

    public function testCountable()
    {
        $this->assertSame(1, count($this->immutable));
    }

    public function testUnserialize()
    {
        $ser = serialize($this->immutable);
        $this->assertEquals($this->immutable, unserialize($ser));
    }

    public function testArrayAccessMethods()
    {
        $mutable = $this->mutable;

        $this->assertTrue($mutable['key']);
        $this->assertTrue(isset($mutable['key']));
        unset($mutable['key']);
        $this->assertNull($mutable['key']);
        $mutable['key'] = true;
        $this->assertTrue($mutable['key']);
    }

    public function testMagicMethods()
    {
        $override = $this->overridable;

        $this->assertTrue($override->key);
        $this->assertTrue(isset($override->key));
        unset($override->key);
        $this->assertNull($override->key);
        $override->key = true;
        $this->assertTrue($override->key);
    }
}
