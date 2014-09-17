<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 14:31
 */

namespace Wave\Framework\Decorator\Decorators;

/**
 * Class Encryption
 * @package Wave\Framework\Decorator
 */
class Encrypt extends BaseDecorator
{

    protected $key = null;
    protected $vector = null;

    public function __construct($key, $vector)
    {

        if (!extension_loaded('mcrypt')) {
            throw new \RuntimeException(
                "MCrypt isn't available."
            );
        }

        if (strlen($key) != ($len = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC))) {
            throw new \InvalidArgumentException(
                sprintf("Supplied key is invalid. Expected %d, got %d", $len, strlen($key))
            );
        }
        $this->key = $key;

        if (strlen($vector) != ($len = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC))) {
            throw new \InvalidArgumentException(
                sprintf("Supplied vector is invalid. Expected %d, got %d", $len, strlen($vector))
            );
        }
        $this->vector = $vector;
    }
    public function call()
    {
        $result = array_shift(func_get_args());

        if ($this->hasNext()) {
            $result = $this->next()->call($result);
        }

        $mcrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_256, "", MCRYPT_MODE_CBC, "");
        mcrypt_generic_init($mcrypt, $this->key, $this->vector);
        $result = mcrypt_generic($mcrypt, $result);
        mcrypt_generic_deinit($mcrypt);



        return $result;
    }
}
