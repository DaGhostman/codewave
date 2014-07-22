<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 22/07/14
 * Time: 18:49
 */

namespace Wave\View\Extensions\Layout;


class Extension
{
    private $callable = null;
    protected $parent = null;

    public function __construct($parent)
    {
        $parent->{$this->callable} = $this;
        $this->parent = $parent;
    }

    public function getCallable()
    {
        return $this->callable;
    }

    public function __invoke($template)
    {
        $this->parent->render($template);
    }
}
