<?php
namespace Wave\Framework\Decorator\Decorators;

class SimplePadding
{
    private $char = "\x00";
    private $num = 0;
    private $mode = STR_PAD_RIGHT;


    public function __construct($count, $char = null)
    {
        if ($char !== null) {
            $this->char = $char;
        }

        $this->num = $count;
    }

    public function commit($value)
    {
        return str_pad($value, $this->num, $this->char, $this->mode);
    }

    public function rollback($value)
    {
        return trim($value, $this->char);
    }
}
