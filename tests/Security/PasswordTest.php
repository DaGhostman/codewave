<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 26/06/14
 * Time: 11:49
 */


/**
 * Class HashTest
 * @package Tests\Security
 *
 * @requires PHP 5.3.7
 */
namespace Tests\Security;


use Wave\Framework\Security\Password;

class PasswordTest extends \PHPUnit_Framework_TestCase
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

    public function testHashLength()
    {
        $obj = new Password($this->options);

        $this->assertEquals(60, strlen($obj->hash("simple")));
    }

    public function testRawSalt()
    {
        $obj = new Password(array('salt' => "123456789012345678901".chr(0)));

        $this->assertEquals(60, strlen($obj->hash('simple')));
    }

    public function testPasswordVerification()
    {
        $hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        $h = new Password();
        $this->assertTrue($h->verify('password', $hash));

        $this->assertFalse($h->verify('password1', $hash));
        $this->assertFalse($h->verify('password', $hash.'1'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadCostInstantiation()
    {
        $h = new Password(array('cost' => 55));
        $h->hash("badCall");
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBadSaltInstantiation()
    {
        $h = new Password(array('salt' => 'asdsdf'));
        $h->hash("badCall");
    }

    public function testCustomSaltGeneration()
    {
        $h = new Password();
        $h->hash('password');

        define('PHALANGER', true);
        $obj = new Password();

        $this->assertEquals(60, strlen($obj->hash('simple')));
    }

    public static function provideCases() {
        return array(
            array('foo', 0, array(), false),
            array('foo', 1, array(), true),
            array('$2y$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi', PASSWORD_BCRYPT, array(), true),
            array('$2y$07$usesomesillystringfore2udlvp1ii2e./u9c8sbjqp8i90dh6hi', PASSWORD_BCRYPT, array('cost' => 7), false),
            array('$2y$07$usesomesillystringfore2udlvp1ii2e./u9c8sbjqp8i90dh6hi', PASSWORD_BCRYPT, array('cost' => 5), true),
        );
    }

    /**
     * @dataProvider provideCases
     */
    public function testCases($hash, $algo, $options, $valid) {
        $obj = new Password();
        $this->assertEquals($valid, $obj->needsRehash($hash, $algo, $options));
    }

    public static function provideInfo() {
        return array(
            array('foo', array('algo' => 0, 'algoName' => 'unknown', 'options' => array())),
            array('$2y$', array('algo' => 0, 'algoName' => 'unknown', 'options' => array())),
            array('$2y$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi', array('algo' => PASSWORD_BCRYPT, 'algoName' => 'bcrypt', 'options' => array('cost' => 7))),
            array('$2y$10$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi', array('algo' => PASSWORD_BCRYPT, 'algoName' => 'bcrypt', 'options' => array('cost' => 10))),
        );
    }

    /**
     * @dataProvider provideInfo
     */
    public function testInfo($hash, $info) {

        $obj = new Password();
        $this->assertEquals($info, $obj->getInfo($hash));
    }
}
