<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 22/09/14
 * Time: 23:07
 */

namespace Wave\Framework\View;

/**
 * Class AbstractInterface
 * @package Wave\Framework\View
 *
 * Abstract class defining the base methods a class wrapping a view should
 * have implemented for convenient and carefree use in applications
 */
abstract class AbstractWrapper
{
    /**
     * @var array Holds view variables
     */
    protected $data = array();

    /**
     * Creates an instance of the object to bootstrap the view
     *
     * @param $templates string Path to the 'templates' directory
     */
    abstract public function __construct($templates);

    /**
     * Load an extension to the view processor
     * @param $extension mixed Extension to add to the core
     *
     * @return $this
     */
    abstract public function loadExtension($extension);

    /**
     * Parses the template and returns is asa string
     * @param $template string Name of the template to render
     *
     * @return string
     */
    abstract public function render($template);

    /**
     * Echo the template directly to the screen instead of returning
     * it as a string
     * @param $template string Name of the template to render
     *
     * @return null
     */
    abstract public function display($template);

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    abstract public function getInstance();
}
