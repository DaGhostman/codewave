<?php

namespace Wave\View;

use Wave\Pattern\Observer\Subject;

/**
 * Class Engine
 * @package Wave\View
 */
class Engine extends Subject
{
    protected $path = null;
    protected $templates = array();
    protected $templateExtension = 'phtml';

    protected $parser = null;

    private $extensions = array();
    private $data = array();

    /**
     * @param $path string The path to the templates directory
     * @param $parser object The parser for the templates or null
     *
     * @throws \RuntimeException
     */
    public function __construct($path = '../application/templates', $parser = null)
    {
        $this->path = realpath($path);

        if (!is_readable($this->path)) {
            throw new \RuntimeException("Template directory not readable");
        }

        if (!in_array('view', stream_get_wrappers())) {
            stream_register_wrapper('view', '\Wave\View\Stream');
        }
    }

    /**
     * @param $key
     * @param $value
     */
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
     * @param $parser mixed Parser for template syntax.
     * @throws \InvalidArgumentException
     */
    public function setParser($parser)
    {
        if (!is_object($parser)) {
            throw new \InvalidArgumentException(
                'Invalid parser specified'
            );
        }
        $this->parser = $parser;
    }

    /**
     * Magic call method, for extensions
     *
     * @param $name string Name of the extension
     * @param $args mixed extensions arguments
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if ('ext' === strtolower($name)) {
            if (isset($this->extensions[$args[0]])) {
                return $this->extensions[$args[0]];
            }
        }
        return null;
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
                if (is_object($this->parser) && method_exists($this->parser, 'register')) {
                    $this->parser->register('extension', $name, $callable);
                }
            }
        }

        $template->assignAll($this->data);

        if (is_object($this->parser)) {
            $template = $this->parser->parse((string) $template);
        }
        return $template;
    }
}
