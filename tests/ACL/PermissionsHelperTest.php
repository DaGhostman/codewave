<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 19/09/14
 * Time: 17:13
 */

namespace Tests\ACL;

use org\bovigo\vfs\vfsStream;
use Wave\Framework\ACL\Helpers\PermissionsHelper;


class PermissionsHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root = null;

    protected function setUp()
    {
        $structure = array(
            'roles' => array(
                'member.json' => '{"permissions": ["members-area"], "extends": "master"}',
                'master.json' => '{"permissions": ["all"]}'
            )
        );

        $this->root = vfsStream::setup('root', null, $structure);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadPathCreation()
    {
        new PermissionsHelper('/some/bad/path');
    }

    public function testCreationFromFile()
    {
        $perm = new PermissionsHelper('vfs://root/roles');
        $this->assertSame(array('all', 'members-area'), $perm->fromFile('member'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadCreationFromFile()
    {
        $perm = new PermissionsHelper($this->root->url('roles'));
        $perm->fromFile('cyborg');
    }

    public function testGeneratinFromString()
    {
        $perm = new PermissionsHelper('vfs://root/roles');
        $this->assertSame(
            array('all', 'members-area', 'update-content'),
            $perm->fromString('{"permissions": ["update-content", "members-area"], "extends": "member"}')
        );
    }

    public function testInherits()
    {
        $perm = new PermissionsHelper('vfs://root/roles');
        $perm->fromFile('member');
        $this->assertTrue($perm->inherits('master'));
    }
}
