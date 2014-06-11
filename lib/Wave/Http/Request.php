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
     * Note that $_REQEUST may include the contants of the $_COOKIE,
     * consult your php.ini for more infomation.
     *
     * @param array $params
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
     *            The key coresponding to the parameter
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
     * Adds external list of parameters.
     * (Added in favor of routes adding restful params)
     *
     * @param array $params
     *            Array of new parameters to add
     */
    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
        
        return $this;
    }

    /**
     * Is the reuqest GET
     * 
     * @return boolean
     */
    public function isGet()
    {
        return ('GET' == $this->env['request.method']);
    }

    /**
     * Is the request POST
     * 
     * @return boolean
     */
    public function isPost()
    {
        return ('POST' == $this->env['request.method']);
    }

    /**
     * Is the the request PUT
     * 
     * @return boolean
     */
    public function isPut()
    {
        return ('PUT' == $this->env['request.method']);
    }

    /**
     * Is the request DELETE
     * 
     * @return boolean
     */
    public function isDelete()
    {
        return ('DELETE' == $this->env['request.method']);
    }

    /**
     * Is the request HEAD
     * 
     * @return boolean
     */
    public function isHead()
    {
        return ('HEAD' == $this->env['request.method']);
    }

    /**
     * Is the request TRACE
     * 
     * @return boolean
     */
    public function isTrace()
    {
        return ('TRACE' == $this->env['request.method']);
    }

    /**
     * Is the reuqest OPTIONS
     * 
     * @return boolean
     */
    public function isOptions()
    {
        return ('OPTIONS' == $this->env['request.method']);
    }

    /**
     * Is the request CONNECT
     * 
     * @return boolean
     */
    public function isConnect()
    {
        return ('CONNECT' == $this->env['request.method']);
    }

    /**
     * Is the reuqest an AJAX request
     * 
     * @return boolean
     */
    public function isAjax()
    {
        if (array_key_exists('X_REQUESTED_WITH', $_SERVER)) {
            if ('XMLHttpRequest' === $_SERVER['X_REQUESTED_WITH']) {
                return true;
            }
        }
        
        return false;
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
     * @param unknown $header            
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
