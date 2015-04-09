<?php

namespace Test\Common;


use Wave\Framework\Common\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type Repository
     */
    protected $repo;

    protected function setUp ()
    {
        $this->repo = Repository::getInstance(['stub1' => 'demo']);
    }

    public function testRepositoryBootstrapping ()
    {
        $this->assertSame('demo', $this->repo['stub1']);
        $this->assertSame('demo', $this->repo->invoke('stub1'));
    }

    public function testNewInstance ()
    {
        $this->assertNotSame(spl_object_hash($this->repo), spl_object_hash(Repository::newInstance()));
        $r = $this->repo;
        $this->assertNotSame(spl_object_hash($r), spl_object_hash($r::newInstance()));
    }

    public function testCloning ()
    {
        $ref = new \ReflectionObject($this->repo);
        $method = $ref->getMethod('__clone');
        $method->setAccessible(true);
        $this->setExpectedException('\LogicException', 'Class not cloneable');
        $method->invoke($this->repo);
    }

    public function testDestroy ()
    {
        $repo = $this->$repo;
    }

}
