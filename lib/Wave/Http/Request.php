<?php
namespace Wave\Http;

class Request
{

    protected $params = array();

    protected $env = null;

    protected $headers = array();

    /**
     * Builds the params array, Appends the contents of $_REQUEST to the params list.
     *
     *
     * Note that $_REQEUST may include the constants of the $_COOKIE,
     * consult your php.ini for more information.
     *
     * @param mixed $environment
     *            Array of parameters which are passed via the URI
     */
    public function __construct($environment)
    {
        $this->params = array_merge($this->params, $_REQUEST);
        
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                
                $kkey = str_replace('_', ' ', substr($key, 5));
                $header = str_replace(' ', '', ucwords(strtolower($kkey)));
                
                $this->headers[$header] = $value;
            }
        }
        
        $this->env = $environment;
    }

    /**
     * Getter for request parameters
     *
     * @param string $key
     *            The key corresponding to the parameter
     * @return mixed The param value or null, if $key does not exist
     */
    public function param($key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        
        return null;
    }

    /**
     * @param $name string Called function name
     * @param $args array Arguments passed during the call
     *
     * @return bool
     */
    public function __call($name, $args = array())
    {
        if ('is' == substr($name, 0, 2)) {
            switch (($method = ucfirst(substr($name, 2)))) {
                case 'Ajax':
                    if (array_key_exists('X_REQUESTED_WITH', $_SERVER)) {
                        if ('XMLHttpRequest' === $_SERVER['X_REQUESTED_WITH']) {
                            return true;
                        }
                    }

                    return false;
                    break;
                default:
                    return (strtoupper($method) == $this->env['request.method']);
                    break;
            }
        }
    }

    /**
     * Adds external list of parameters.
     * (Added in favor of routes adding restful params)
     *
     * @param array $params
     *            Array of new parameters to add
     *
     * @return Request
     */
    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
        
        return $this;
    }

    /**
     *
     * @return string The method being used for the current request
     */
    public function getMethod()
    {
        return $this->env['request.method'];
    }

    /**
     * Getter for headers.
     * Returns the current request headers, based on key. Note that the $key syntax is:
     * AcceptEncoding for the header Accept-Encoding, however values are untouched.
     *
     * @param string $header
     * @return mixed The header value on success, null otherwise
     */
    public function getHeader($header)
    {
        if (array_key_exists($header, $this->headers)) {
            return $this->headers[$header];
        }
        
        return null;
    }
}
