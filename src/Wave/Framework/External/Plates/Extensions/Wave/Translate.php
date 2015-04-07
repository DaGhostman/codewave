<?php

namespace Wave\Framework\External\Plates\Extensions\Wave;

use \League\Plates\Engine;
use \League\Plates\Extension\ExtensionInterface;
use \Wave\Framework\Service\View\Translation;

class Translate implements ExtensionInterface
{

    /**
     * @type Translation
     */
    private $translator;

    public function __construct($translator)
    {
        if (!$translator instanceof Translation) {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument to be instance of Service\View\Translation, "%s" given',
                gettype($translator)
            ));

            $this->translator = $translator;
        }
    }

    public function register(Engine $engine) {
        $engine->registerFunction('translate', [$this, 'translate']);
        $engine->registerFunction('plural', [$this, 'plural']);
    }

    public function translate($string) {
        return $this->translator->translate($string);
    }

    public function pluralize($string, $num = 2, $format = null)
    {
        return $this->translator->plural($string, $num, $format);
    }
}