<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 29/09/14
 * Time: 13:38
 */

namespace Wave\Framework\View\Smarty;


use Wave\Framework\View\AbstractWrapper;

class Wrapper extends AbstractWrapper
{

    protected $instance = null;

    /**
     * Creates an instance of the object to bootstrap the view
     *
     * @param $templates string Path to the 'templates' directory
     * @throws \InvalidArgumentException
     */
    public function __construct ($templates)
    {
        if (!is_dir($templates)) {
            throw new \InvalidArgumentException(
                sprintf('%s is not readable template source', $templates)
            );
        }

        $this->instance = new \Smarty();
        $this->instance->setTemplateDir($templates);
    }

    public function setCacheDir($dir)
    {
        if (!is_dir($dir) && is_writable($dir)) {
            throw new \InvalidArgumentException(
                sprintf('%s is not valid or is not writable template cache dir', $dir)
            );
        }

        $this->instance->setCacheDir($dir);

        return $this;
    }

    public function setCompileDir($dir)
    {
        if (!is_dir($dir) && is_writable($dir)) {
            throw new \InvalidArgumentException(
                sprintf('%s is not valid or is not writable template compile dir', $dir)
            );
        }

        $this->instance->setCompileDir($dir);

        return $this;
    }

    public function __set($key, $value)
    {
        $this->instance->assign($key, $value);
    }

    public function setConfigDir($dir)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(
                sprintf('%s is not valid config dir', $dir)
            );
        }

        $this->instance->setConfigDir($dir);
    }

    /**
     * Load an extension to the view processor
     *
     * @param $extension array Extension to add to the core
     *
     * @return $this
     */
    public function loadExtension($extension)
    {
        $extension = array_merge(array(
            'cacheable' => false,
            'cache_attrs' => null
        ), $extension);


        call_user_func(
            array($this->instance, 'registerPlugin'),
            $extension['type'],
            $extension['name'],
            $extension['callback'],
            $extension['cacheable'],
            $extension['cache_attrs']
        );
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
        return $this->instance->fetch($template);
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
        $this->instance->display($template);
    }

    public function getInstance()
    {
        return $this->instance;
    }
}
