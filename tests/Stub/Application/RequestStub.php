<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 02/02/15
 * Time: 00:35
 */

namespace Stub\Application;


use Wave\Framework\Http\Request;

class RequestStub extends Request
{
    protected $method = null;
    protected $uri = null;
    protected $params = array();

    public function __construct($method, $uri, $params = array())
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->params = $params;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getMethod()
    {
        return $this->method;
    }
}
