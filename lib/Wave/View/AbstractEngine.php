<?php

namespace Wave\View;


abstract class AbstractEngine
{

    /**
     * @param $config array Array with configurations to pass to the engine
     */
    abstract public function __construct($config);

    /**
     * @param $name string Extension name
     * @param $extension callable Extension for the current viewer
     *
     * @return mixed
     */
    abstract public function loadExtension($name, $extension);

    /**
     * @param $name string Alias for the path
     * @param $path string Directory in which templates can be found
     *
     */
    abstract public function register($name, $path);

    /**
     * @param $ext string The extension of the template files
     *
     * @return mixed
     */
    abstract public function setFileExtension($ext);

    /**
     * @param $templateStr string A template string in the format 'alias::file'
     *                            without the file extension
     *
     * @return null
     */
    abstract public function render($templateStr);
}
