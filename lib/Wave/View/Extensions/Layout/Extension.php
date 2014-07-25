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
    private $callable = 'layout';
    protected $parent = null;

    /**
     * Uses this method to register itself to the parent
     *
     * @param $parent object The instance of the view engine object
     */
    public function __construct($parent)
    {
        $parent->{$this->callable} = $this;
        $this->parent = $parent;
    }

    /**
     * Getter for the extension name
     *
     * @return string The name of the extension
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * This is the method which is called by the parser and by the engine.
     *
     * @param $template
     * @param $flag mixed Flag
     *
     * @return mixed
     */
    public function __invoke($template, $flag = false)
    {
        return $this->parent->render($template);
    }
}
