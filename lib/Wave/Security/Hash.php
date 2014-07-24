<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 25/06/14
 * Time: 00:45
 */

namespace Wave\Security;

/**
 * Class Hash
 * @package Wave\Security
 *
 * Hashing functionality using bcrypt implementation compatible to
 *          Ircmaxell's implementation of the password_hash function
 */
class Hash
{

    protected $cycles = 10;
    protected $salt = null;
    /**
     * @param $options array Assoc array of options necessary for hashing
     * @throws \RuntimeException
     */
    public function __construct($options = array())
    {
        if (!function_exists("crypt")) {
            throw new \RuntimeException(
                "Crypt must be loaded"
            );
        }

        $this->cycles = (
            isset($options['cost']) && !is_null($options['cost']) ?
                $options['cost'] : 10
        );

        $this->salt = (
            isset($options['salt']) && !is_null($options['salt']) ?
                $options['salt'] : null
        );
    }

    /**
     * @param $password string A string to be hashed
     * @throws \RuntimeException
     * @return string The hash
     */
    public function hash($password)
    {
        /*
         * Required cost size appropriate
         */
        if ($this->cycles < 4 || $this->cycles > 31) {
            throw new \RuntimeException(
                sprintf("Invalid cost parameter specified %d", $this->cycles)
            );
        }

        if (null === $this->salt) {
            $this->salt = $this->encode($this->generateSalt());
        }

        /*
         * Salt set and with valid size
         */
        if ($this->strlen((string) $this->salt) < 22) {
            throw new \RuntimeException(
                "Invalid salt length"
            );
        }

        if (0 == preg_match("#^[a-zA-Z0-9./]+$#D", $this->salt)) {
            $this->salt = $this->encode($this->salt);
        }

        $result = crypt($password, sprintf('$2y$%02d$', $this->cycles) . $this->substr($this->salt, 0, 22));

        if (!is_string($result) || $this->strlen($result) != 60) {
            throw new \RuntimeException("Unable to generate hash");
        }

        return $result;
    }


    /**
     * Generates a salt from one of the following sources
     *      mcrypt, /dev/urandom or mt_rand()
     *
     * @return string The hash that is going to be used
     */
    public function generateSalt()
    {
        if (!defined('PHALANGER')) {
            if (function_exists('mcrypt_create_iv')) {
                return mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
            }

            if (is_readable('/dev/urandom')) {
                $fp = fopen('/dev/urandom', 'r');
                $buffer = null;

                while ($this->strlen($buffer) < 16) {
                    $buffer .= fread($fp, 16 - $this->strlen($buffer));
                }

                fclose($fp);

                if ($this->strlen($buffer) >= 16) {
                    return $buffer;
                }
            }
        }


        $buffer = null;
        for ($i=0; $i<16; $i++) {
            $buffer .= chr(mt_rand(0, 255));
        }

        return $buffer;
    }

    public function encode($string)
    {
        $base64_digits =
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits =
            './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        return strtr(
            rtrim(base64_encode($string), '='),
            $base64_digits,
            $bcrypt64_digits
        );
    }

    /**
     * @param $string string to measure
     * @return string
     */
    public function strlen($string)
    {
        $result =  strlen($string);
        if (function_exists('mb_strlen')) {
            $result =  mb_strlen($string, '8bit');
        }

        return $result;
    }

    /**
     * @param $string string the string to cut
     * @param $start int Starting point
     * @param $length int End point
     *
     * @return string
     */
    public function substr($string, $start, $length)
    {
        $result = substr($string, $start, $length);
        if (function_exists('mb_substr')) {
            $result =  mb_substr($string, $start, $length);
        }

        return $result;
    }

    public function verify($password, $hash)
    {
        $result = crypt($password, $hash);

        if (!is_string($result) ||
            $this->strlen($result) != $this->strlen($hash) ||
            $this->strlen($result) <= 13
        ) {

            return false;
        }

        $status = 0;
        for ($i=0; $i < $this->strlen($result); $i++) {
            $status |= (ord($result[$i]) ^ ord($hash[$i]));
        }

        return $status === 0;
    }
}
