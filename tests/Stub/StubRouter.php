<?php
namespace Stub;

use Wave\Framework\Application\Router;
use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;

class StubRouter extends Router
{
    private $output;

    public function __construct($output)
    {
        $this->output = $output;
    }
    public function dispatch(RequestInterface $req, ResponseInterface $resp)
    {
        echo $this->output;
    }
}