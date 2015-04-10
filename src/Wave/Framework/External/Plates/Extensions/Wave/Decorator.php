<?php

namespace Wave\Framework\External\Plates\Extensions\Wave;

use \League\Plates\Engine;
use \League\Plates\Extension\ExtensionInterface;
use Wave\Framework\Decorator\Decorator as Chain;

class Decorator implements ExtensionInterface
{

    private $decorators = [];
    public function __construct($decorator)
    {
        if ($decorator instanceof Chain) {
            array_push($this->decorators, $decorator);
        }

        if (is_array($decorator)) {
            $this->decorators += $decorator;
        }

        array_walk($this->decorators, function ($entry, $key) {
                if (!$entry instanceof Chain) {
                    throw new \UnexpectedValueException(sprintf(
                        'Invalid array member at key "%s".' .
                        'Expected instance of \Wave\Framework\Decorator\Decorator, "%s" received',
                        $key,
                        gettype($entry)
                    ));
                }
            });
        $this->decorators = $decorator;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('decorateValue', [$this, 'decorate']);
    }

    public function decorate($value, $chainName = null)
    {
        if ($chainName === null) {
            if (isset($this->decorators[0]) && $this->decorators[0] !== null) {
                return $this->decorators[0]->rollback($value);
            }

            throw new \RuntimeException('No default decorator defined');
        }

        if (array_key_exists($chainName, $this->decorators)) {
            return $this->decorators[$chainName]->rollback($value);
        }

        throw new \InvalidArgumentException(sprintf(
            'Decorator with name "%s" does not exist',
            $chainName
        ));
    }
}
