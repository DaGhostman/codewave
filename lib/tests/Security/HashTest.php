<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 26/06/14
 * Time: 11:49
 */

namespace Tests\Security;

/**
 * Class HashTest
 * @package Tests\Security
 *
 * @requires PHP 5.3.7
 */
class HashTest extends \PHPUnit_Framework_TestCase
{
    private $obj = null;
    private $options = array(
        'cost' => 10,
        'salt' => '0123456789abcdefghijklm'
    );

    protected function setUp()
    {
        if (strnatcmp(phpversion(), '5.5') < 0) {
            $hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
            $test = crypt("password", $hash);
            if ($test != $hash) {
                $this->markTestSkipped("Unable to verify that the $2y fix");
            }
        }
    }

    public function testHashing()
    {
        $obj = new \Wave\Security\Hash($this->options);

        $this->assertSame(
            password_hash('mypassword', PASSWORD_DEFAULT, $this->options),
            $obj->hash('mypassword')
        );
    }

    public function testHashLength()
    {
        $obj = new \Wave\Security\Hash($this->options);

        $this->assertEquals(60, strlen($obj->hash("simple")));
    }

    public function testRawSalt()
    {
        $obj = new \Wave\Security\Hash(array('salt' => "123456789012345678901".chr(0)));

        $this->assertEquals(60, strlen($obj->hash('simple')));
    }

    public function testPasswordVerification()
    {
        $hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        $h = new \Wave\Security\Hash();
        $this->assertTrue($h->verify('password', $hash));

        $this->assertFalse($h->verify('password1', $hash));
        $this->assertFalse($h->verify('password', $hash.'1'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadCostInstantiation()
    {
        $h = new \Wave\Security\Hash(array('cost' => 55));
        $h->hash("badCall");
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadSaltInstantiation()
    {
        $h = new \Wave\Security\Hash(array('salt' => 'asdsdf'));
        $h->hash("badCall");
    }

    public function testCustomSaltGeneration()
    {
        $h = new \Wave\Security\Hash();
        $h->hash('password');

        define('PHALANGER', true);
        $obj = new \Wave\Security\Hash();

        $this->assertEquals(60, strlen($obj->hash('simple')));
    }

}
