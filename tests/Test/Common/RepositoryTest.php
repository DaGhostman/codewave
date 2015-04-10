<?php

namespace Test\Common;


use Wave\Framework\Common\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type Repository
     */
    protected $repo;

    protected function setUp()
    {
        $this->repo = Repository::getInstance(['stub1' => 'demo']);
    }

    public function testRepositoryBootstrapping ()
    {
        $this->assertSame('demo', $this->repo['stub1']);
        $this->assertSame('demo', $this->repo->invoke('stub1'));
        $this->assertSame('demo', $this->repo->stub1());
    }

    public function testNewInstance()
    {
        $this->assertNotSame(spl_object_hash($this->repo), spl_object_hash(Repository::newInstance()));
        $r = $this->repo;
        $this->assertNotSame(spl_object_hash($r), spl_object_hash($r::newInstance()));
    }

    public function testCloning()
    {
        $ref = new \ReflectionObject($this->repo);
        $method = $ref->getMethod('__clone');
        $method->setAccessible(true);
        $this->setExpectedException('\LogicException', 'Class not cloneable');
        $method->invoke($this->repo);
    }

    public function testDestroy()
    {
        $this->repo->destroy();
        $this->assertFalse(isset($this->repo['demo']));
        $this->assertNotSame(spl_object_hash($this->repo), spl_object_hash(Repository::getInstance()));
        $this->assertSame(0, count($this->repo));
    }

    public function testBadInvokeMethodCall()
    {
        $this->setExpectedException('\LogicException', 'Trying to access non-declared entry');
        $this->assertNull($this->repo->invoke('some-bad-name'));

    }

    public function testBadInvokeArrayAccess()
    {
        $this->setExpectedException('\LogicException', 'Trying to access non-declared entry');
        $this->repo['another-bad-name'];
    }

    public function testStaticDefinitions()
    {
        $this->assertTrue($this->repo->singleton('test', function() {
            return 'OK';
        }));

        $this->assertSame('OK', $this->repo->test());
    }

    protected function tearDown()
    {
        $this->repo->destroy();
    }
}
