<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 19/09/14
 * Time: 15:36
 */
namespace Tests\ACL;

use org\bovigo\vfs\vfsStream;
use Wave\Framework\ACL\MAC;

class MacTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root = null;

    protected function setUp()
    {
        $structure = array(
            'groups' => array(
                'editors.json' => '{"permissions": ["edit", "delete"]}',
                'writers.json' => '{"permissions": ["create"], "extends": "editors"}'
            ),
            'roles' => array(
                'member.json' => '{"permissions": ["members-area"]}'
            )
        );

        $this->root = vfsStream::setup('root', null, $structure);

    }

    public function testSame()
    {
        $this->assertSame(MAC::getInstance(), MAC::getInstance());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPathException()
    {
        MAC::getInstance()->setPath('path/that/does/not/exist');
    }

    public function testRoleAssign()
    {
        $std = new \StdClass;


        MAC::getInstance()->setPath($this->root->url('/groups'))
            ->assign($std, 'member');

        $this->assertTrue(MAC::getInstance()->role($std)->hasAccess('members-area'));
    }

    public function testGroupAssign()
    {
        $std = new \StdClass;


        MAC::getInstance()->setPath($this->root->url('/groups'))
            ->assign($std, null, 'writers');

        $this->assertTrue(MAC::getInstance()->group($std)->isAllowed('edit'));
        $this->assertTrue(MAC::getInstance()->group($std)->isAllowed('delete'));
        $this->assertTrue(MAC::getInstance()->group($std)->isAllowed('create'));
        $this->assertTrue(MAC::getInstance()->group($std)->isDenied('hack'));
    }

    public function testRoleGroupAssign()
    {
        $std = new \StdClass;


        MAC::getInstance()->setPath($this->root->url('/groups'))
            ->assign($std, 'member', 'writers');

        $this->assertTrue(MAC::getInstance()->group($std)->isAllowed('edit'));
        $this->assertTrue(MAC::getInstance()->role($std)->hasAccess('members-area'));
        $this->assertTrue(MAC::getInstance()->group($std)->isAllowed('delete'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadObjectAssign()
    {
        MAC::getInstance()->assign('string');
    }

    /**
     * @expectedException \LogicException
     */
    public function testExistingObjectAssign()
    {
        $std = new \stdClass;
        MAC::getInstance()->assign($std);
        MAC::getInstance()->assign($std);
    }

    public function testUnassignedGroupRole()
    {
        $this->assertNull(MAC::getInstance()->group(new \stdClass()));
        $this->assertNull(MAC::getInstance()->role(new \stdClass()));
    }
} 
