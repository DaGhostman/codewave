<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 22/09/14
 * Time: 23:37
 */

namespace Wave\Framework\View\Twig;

use Wave\Framework\View\AbstractWrapper;

/**
 * A class wrapping up the access to Twig
 *
 * Class Wrapper
 * @package Wave\Framework\View\Twig
 */
class Wrapper extends AbstractWrapper
{

    protected $options = array(
        'charset' => 'utf-8',
        'debug' => false,
        'cache' => false,
        'auto_reload' => true,
        'strict_variables' => true
    );

    /**
     * @var array Data to pass to the template
     */
    protected $data = array();

    /**
     * @var \Twig_Loader_Filesystem
     */
    protected $loader = null;

    /**
     * @var \Twig_Environment
     */
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

        $this->loader = new \Twig_Loader_Filesystem($templates);
    }

    /**
     * Wraps \Twig_Loader_Filesystem directly
     *
     * @return $this
     */
    public function addPath()
    {
        call_user_func_array(array($this->loader, 'addPath'), func_get_args());

        return $this;
    }

    /**
     * Enables debugging of the viewer (if supported)
     * @param $state bool true to enable or false to disable
     *
     * @return $this
     */
    public function debug($state = true)
    {
        $this->options['debug'] = $state;

        return $this;
    }

    /**
     * Sets the path to use for caching
     * @param $path string absolute path to the directory in which to store the cache
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function cachePath($path)
    {
        if (!realpath($path)) {
            throw new \InvalidArgumentException(
                sprintf('Supplied path is invalid: %s', $path)
            );
        }

        $this->options['cache'] = $path;

        return $this;
    }

    /**
     * Sets the charset to use when parsing the templates
     *
     * @param $charset string The new charset to use
     *
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->options['charset'] = $charset;

        return $this;
    }

    /**
     * changes the value of the option 'auto_reload'
     * @param $state bool True to enable, false - disable
     *
     * @return $this
     */
    public function autoReload($state)
    {
        $this->options['auto_reload'] = $state;

        return $this;
    }


    /**
     * Load an extension to the view processor
     *
     * @param $extension \Twig_Extension Extension to add to the environment
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function loadExtension($extension)
    {
        if (!$extension instanceof \Twig_Extension) {
            throw new \InvalidArgumentException(
                sprintf("%s is not a valid Twig Extension", get_class($extension))
            );
        }

        if (!$this->instance instanceof \Twig_Environment) {
            $this->createInstance();
        }

        $this->getInstance()
            ->addExtension($extension);

        return $this;
    }

    /**
     * Creates instance of \Twig_Environment and saves it.
     * Before creating the instance, make sure that all options are set accordingly
     * to your requirement.
     *
     * @throws \RuntimeException
     */
    public function createInstance()
    {
        if ($this->instance instanceof \Twig_Environment) {
            throw new \RuntimeException(
                "Viewer already instantiated"
            );
        }

        $this->instance = new \Twig_Environment($this->loader, $this->options);
    }

    /**
     * Getter for \Twig_Environment
     *
     * @return \Twig_Environment
     */
    public function getInstance()
    {
        if (!$this->instance instanceof \Twig_Environment) {
            $this->createInstance();
        }

        return $this->instance;
    }

    /**
     * Returns the Twig loader instance
     *
     * @return \Twig_Loader_Interface
     */
    public function getLoader()
    {
        return $this->loader;
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
        return $this->getInstance()
            ->render($template, $this->data);
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
}
