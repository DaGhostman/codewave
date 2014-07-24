<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 21/07/14
 * Time: 17:57
 */

namespace Wave\View\Extensions\HTML\Components;

/**
 * Class Script
 * @package Wave\View\Extensions\HTML\Components
 */
class Script
{
    protected $content = '';
    protected $options = null;

    protected $doc = null;

    /**
     * @param       $doc
     * @param array $options
     */
    public function __construct($doc, $options = array())
    {
        $this->doc = $doc;
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getScript()
    {
        $doc = $this->doc;
        $options = $this->options;

        $script = (isset($options['script']) ?$options['script'] : '');

        $element = $doc->createElement('script', $script);

        if (isset($options['script'])) {
            unset($options['script']);
        }


        foreach ($options as $key => $value) {
            $element->setAttribute($key, $value);
        }

        return $element;
    }
}
