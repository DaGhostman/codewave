<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 15:56
 */

namespace Wave\Framework\Decorator\Decorators;


class Decrypt extends BaseDecorator
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
                sprintf("Supplied key is invalid. Expected %d, got %d", $len, strlen($vector))
            );
        }
        $this->vector = $vector;
    }

    public function call()
    {
        $args = func_get_args();
        $mcrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_256, "", MCRYPT_MODE_CBC, "");
        mcrypt_generic_init($mcrypt, $this->key, $this->vector);
        $result = mdecrypt_generic($mcrypt, $args[0]);
        mcrypt_generic_deinit($mcrypt);

        if ($this->hasNext()) {
            return $this->next()->call(trim($result));
        }

        return rtrim($result);
    }
}
