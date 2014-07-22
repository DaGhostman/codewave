<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 21/07/14
 * Time: 16:33
 */

namespace Wave\View\Extensions\HTML;


use Wave\Pattern\Observer\Observer;
use Wave\View\Extensions\HTML\Components\Head;

class Extension extends Observer
{
    protected $callable = 'HTML';
    protected $components = array();

    public function __construct($parent)
    {
        parent::__construct($parent);
        $parent->{$this->callable} = $this;
    }

    /**
     * @return string the variable of this extension
     */
    public function getCallable()
    {
        return $this->callable;
    }

    public function head($title = null, $meta = array(), $link = array(), $script = array(), $custom = null)
    {
        if (!isset($this->components['head'])) {
            $this->components['head'] = new Head($title, $meta, $link, $script, $custom);
        }

        return $this->components['head'];
    }
}
