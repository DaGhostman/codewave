<?php

namespace Wave\View;

/**
 * Class Engine
 * @package Wave\View
 */
class Engine extends AbstractEngine
{
    protected $path = null;
    protected $templates = array();
    protected $templateExtension = 'phtml';

    private $extensions = array();
    private $data = array();

    /**
     * @param $path string The path to the templates directory
     *
     * @throws \RuntimeException
     */
    public function __construct($path = '../application/templates')
    {
        $this->path = realpath($path);

        if (!is_readable($this->path)) {
            throw new \RuntimeException("Template directory not readable");
        }

        if (!in_array('view', stream_get_wrappers())) {
            stream_register_wrapper('view', '\Wave\View\Stream');
        }
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param $template string Template name
     * @param $path string Path to the template relative to the root template path
     *
     * @throws \RuntimeException
     */
    public function register($template, $path)
    {
        if (!is_readable(realpath($this->path . $path))) {
            throw new \RuntimeException(
                sprintf("Path for %s template is not readable", $template)
            );
        }
        $this->templates[$template] = realpath($this->path . $path);
    }

    /**
     * @param $name string Name of the extension
     * @param $extension callable an extension for the current viewer
     *
     * @return mixed
     */
    public function loadExtension($name, $extension)
    {
        $this->extensions[$name] = $extension;

        return $this;
    }

    /**
     * @param $ext string The extension of the template files
     *
     * @return mixed
     */
    public function setFileExtension($ext)
    {
        $this->templateExtension = $ext;

        return $this;
    }

    /**
     * @param $templateStr string A template string in the format 'alias::file'
     *                            without the file extension
     *
     * @throws \InvalidArgumentException
     * @return null
     */
    public function render($templateStr)
    {
        list($alias, $tpl)=explode('::', $templateStr);

        if (!array_key_exists($alias, $this->templates)) {
            throw new \InvalidArgumentException("Unknown alias specified");
        }

        $template = new Template($tpl, $this->templates[$alias], $this->templateExtension);

        if (!empty($this->extensions)) {
            foreach ($this->extensions as $name => $callable) {
                $template->addExtension($name, $callable);
            }
        }

        $template->assignAll($this->data);

        return $template;
    }
}
