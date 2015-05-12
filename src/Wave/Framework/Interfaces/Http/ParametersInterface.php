<?php


namespace Wave\Framework\Interfaces\Http;


interface ParametersInterface
{
    public function __construct(RequestInterface $request);
    public function fetch($name);
    public function has($name);
    public function set($name, $value);
    public function remove($name);
}