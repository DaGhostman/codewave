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

    public function translate($id, $plural = false)
    {
        if (!array_key_exists($id, $this->source)) {
            return $id;
        }

        return ($plural ? $this->plural($this->source[$id], $plural[0], $plural[1]) : $this->source[$id]);
    }

    /**
     * Convert the string to plural based on the choices
     *
     * @param $id mixed
     * @param $num int
     * @param $format
     *
     * @return string
     */
    public function plural($id, $num = 1, $format = '%s %s')
    {
        $translated = $this->translate($id);

        if (array_key_exists($translated, $this->plurals)) {
            $translated = $this->plurals[$translated];
            if (is_array($translated)) {
                if ($num !== 1) {
                    $translated = $translated[1];
                } else {
                    $translated = $translated[0];
                }
            }
        }

        if (array_key_exists($num, $this->plurals)) {
            $num = $this->plurals[$num];
        }

        return sprintf($format, $num, $translated);
    }
}
