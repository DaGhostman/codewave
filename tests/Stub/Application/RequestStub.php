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
    public function __construct($method, $uri)
    {
        $this->method = $method;
        $this->uri = $uri;
    }
    public function uri()
    {
        return $this->uri;
    }

    public function method()
    {
        return $this->method;
    }
}
