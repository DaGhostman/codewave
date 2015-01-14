<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 24/08/14
 * Time: 00:17
 */

namespace Wave\Framework\Http;


class Request
{

    protected $request = array();
    protected $headers = array();
    protected $vars = array();

    protected $source = array();

    protected $params = array();

    /**
     * Creates a request object and accepts an array with key => value
     * pairs which in most cases of Web Apps should be $_SERVER. In scenario where
     * CLI functionality it will require the following keys: 'REQUEST_URI',
     * 'REQUEST_METHOD' and 'REQUEST_PROTOCOL'. The URI should be translated to
     * look like usual HTTP URI and method could be anything, only requirement
     * is that it should be the same as the one assigned to the Controller.
     *
     * @param $source array Array source of information
     */
    public function __construct($source)
    {
        $this->source = $source; // For access with __get

        $this->params = array_merge($_GET, $_POST);
        foreach ($source as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $this->headers[str_replace(
                    ' ',
                    '',
                    ucwords(
                        strtolower(
                            str_replace('_', ' ', substr($name, 5))
                        )
                    )
                )] = $value;

            } elseif (substr($name, 0, 8) == 'REQUEST_') {
                $this->request[str_replace(
                    ' ',
                    '',
                    ucwords(
                        strtolower(
                            str_replace('_', ' ', substr($name, 8))
                        )
                    )
                )] = $value;
            }
        }
    }

    /**
     * A getter for all entries which begin with 'REQUEST_' in the $_SERVER variable.
     *
     * @param $name string The string to search for. Example 'UserAgent' for 'REQUEST_USER_AGENT'
     *
     * @return mixed The value or null if the key is not found
     */
    public function request($name)
    {
        if (!array_key_exists($name, $this->request)) {
            return null;
        }

        return $this->request[$name];
    }

    /**
     * Alias of Request::request('Uri');
     *
     * @return mixed
     */
    public function uri()
    {
        return $this->request['Uri'];
    }

    /**
     * Alias of Request::request('Method');
     *
     * @return mixed
     */
    public function method()
    {
        return $this->request['Method'];
    }

    /**
     * Alias of Request::request('Protocol');
     *
     * @return mixed
     */
    public function protocol()
    {
        return $this->request['Protocol'];
    }

    /**
     * Gives access to 'HTTP_' from $_SERVER
     *
     * @param $name string Header name. Example 'AcceptEncoding'
     *
     * @return null
     */
    public function header($name)
    {
        if (!array_key_exists($name, $this->headers)) {
            return null;
        }

        return $this->headers[$name];
    }

    public function headers()
    {
        return $this->headers;
    }

    /**
     * Returns the requested parameter. Looks up in $_GET and $_POST. Note:
     * if there is a duplicate key an array is returned array(0 => GET, 1 => POST)
     *
     * @param $name string
     *
     * @return mixed
     */
    public function param($name)
    {
        if (!array_key_exists($name, $this->params)) {
            return null;
        }

        return $this->params[$name];
    }

    /**
     * Returns all parameters of the request
     *
     * @return array
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * @param $vars array Array with the URL variables.
     */
    public function setVariables($vars)
    {
        array_merge($this->vars, $vars);
    }

    /**
     * @param $name string Returns a specific URL variables, or null
     * @return mixed null if the key does not exist
     */
    public function variable($name)
    {
        if (array_key_exists($name, $this->vars)) {
            return null;
        }

        return $this->vars['name'];
    }

    /**
     * @return array All the URL variables
     */
    public function variables()
    {
        return $this->vars;
    }

    public function __get($key)
    {
        if (!array_key_exists(strtoupper($key), $this->source)) {
            return null;
        }

        return $this->source[strtoupper($key)];
    }
}
