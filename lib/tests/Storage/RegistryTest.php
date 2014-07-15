<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 14/07/14
 * Time: 18:57
 */

namespace Tests\Storage;


use Wave\Storage\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
    private $mutable = null;
    private $immutable = null;
    private $overridable = null;

    protected function setUp()
    {
        $this->mutable = new Registry(array(
            'mutable' => true,
            'override' => false,
            'data' => array('key' => true)
        ));

        $this->immutable = new Registry(array(
            'mutable' => false,
            'data' => array('key' => true),
            'override' => false
        ));

        $this->overridable = new Registry(array(
            'mutable' => true,
            'override' => true,
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
        $this->assertFalse($mutable->set('key', false));

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
        $mutable = clone $this->mutable;

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

    public function testSerialize()
    {
        $this->assertSame('C:21:"Wave\Storage\Registry":77:{a:3:{s:7:"mutable";b:1;s:8:"override";b:0;s:7:"storage";a:1:{s:3:"key";b:1;}}}', serialize($this->mutable));
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
