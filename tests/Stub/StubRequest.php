<?php


namespace Stub;


use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest as Request;

class StubRequest extends Request implements ServerRequestInterface
{
}