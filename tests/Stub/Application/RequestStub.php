<?php

namespace Stub\Application;


use Wave\Framework\Http\Server\Request;
use Wave\Framework\Http\Uri;

class RequestStub extends Request
{
    protected $method = null;
    protected $uri = null;

    public function __construct($method, $uri, $params = array())
    {
        $this->method = $method;
        $this->uri = new Uri($uri);
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

    public function param($param)
    {
        return $this->$param;
    }
}
