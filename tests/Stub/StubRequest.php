<?php


namespace Stub;


use Wave\Framework\Http\Request;
use Wave\Framework\Interfaces\Http\UrlInterface;

class StubRequest extends Request
{
    public function __construct($method = 'GET', UrlInterface $query = null, array $headers = [], $input = 'php://input') {}
    public function getBody()
    {
        return json_encode([
            'key' => 'value'
        ]);
    }
}