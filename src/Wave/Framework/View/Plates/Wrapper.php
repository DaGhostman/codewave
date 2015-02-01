<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 29/09/14
 * Time: 13:38
 */

namespace Wave\Framework\View\Plates;

use League\Plates\Engine;
use Wave\Framework\View\AbstractWrapper;

class Wrapper extends AbstractWrapper
{

    protected $instance = null;

    protected $data = array();

    /**
     * Creates an instance of the object to bootstrap the view and
     * defines sets the default template extension to 'tpl', to overwrite it
     * use Wrapper::setTemplateExtension
     *
     * @param string $templates Path to templates
     * @throws \InvalidArgumentException
     */
    public function __construct($templates)
    {
        if (!is_dir($templates)) {
            throw new \InvalidArgumentException(
                sprintf('%s is not readable template source', $templates)
            );
        }

        $this->instance = new Engine($templates, 'tpl');
    }

    public function addPath()
    {
        call_user_func_array(array($this->instance, 'addFolder'), func_get_args());

        return $this;
    }

    public function setTemplatesExtension($extension)
    {
        $this->instance->setFileExtension($extension);
    }

    /**
     * Load an extension to the view processor
     *
     * @param $extension mixed Extension to add to the core
     *
     * @return $this
     */
    public function loadExtension($extension)
    {
        $this->instance->loadExtension($extension);

        return $this;
    }

    /**
     * Parses the template and returns is asa string
     *
     * @param $template string Name of the template to render
     *
     * @return string
     */
    public function render($template)
    {
        return $this->instance->render($template, $this->data);
    }

    /**
     * Echo the template directly to the screen instead of returning
     * it as a string
     *
     * @param $template string Name of the template to render
     *
     * @return null
     */
    public function display($template)
    {
        echo $this->render($template);
    }

    public function getInstance()
    {
        return $this->instance;
    }
}
