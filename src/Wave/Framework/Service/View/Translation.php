<?php
namespace Wave\Framework\Service\View;

class Translation
{
    /**
     * @type array
     */
    private $source;

    /**
     * @type array
     */
    private $plurals;

    public function __construct(array $translation, array $plurals = [])
    {
        $this->source = $translation;
        $this->plurals = $plurals;
    }

    public function translate($string)
    {
        if (!array_key_exists($string, $this->source)) {
            return $string;
        }

        return $this->source[$string];
    }

    /**
     * Convert the string to plural based on the choices
     *
     * @param $string mixed
     * @param $num int
     * @param string $format
     *
     * @return string
     */
    public function plural($string, $num = 1, $format = null)
    {
        $format = ($format !== null) ? $format : '%d %s';
        $translated = $this->translate($string);

        if (array_key_exists($translated, $this->plurals)) {
            $translated = $this->plurals[$translated];
            if (is_array($translated)) {
                $translated = $translated[0];

                if ($num !== 1) {
                    $translated = $translated[1];
                }
            }
        }

        if (array_key_exists($num, $this->plurals)) {
            $num = $this->plurals[$num];
        }

        return sprintf($format, $num, $translated);
    }
}
