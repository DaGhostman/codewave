<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 19/09/14
 * Time: 17:11
 */

namespace Tests\ACL;

use org\bovigo\vfs\vfsStream;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root = null;

    protected function setUp()
    {
        $structure = array(
            'roles' => array(
                'member.json' => '{"permissions": ["members-area"]}'
            )
        );

        $this->root = vfsStream::setup('root', null, $structure);
    }

    public function testStub()
    {
        $this->assertTrue(true);
    }
}
