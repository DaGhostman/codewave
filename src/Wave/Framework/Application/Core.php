<?php
/**
 * @author Dimitar
 * @copyright 2015 Dimitar Dimitrov <daghostman.dimitrov@gmail.com>
 * @package codewave
 * @license MIT
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Wave\Framework\Application;

use Go\Aop\Pointcut;
use Wave\Framework\Annotations\General\Catchable;
use Wave\Framework\Http\Server;
use Wave\Framework\Http\Url;
use Wave\Framework\Interfaces\Middleware\MiddlewareInterface;

class Core extends AspectsKernel
{

    private $defaultConfiguration = [
        'debug' => false
    ];

    private $middleware = [];

    /**
     * @var Server
     */
    private $server;

    /**
     * Prepares the core HTTP objects
     */
    public function __construct()
    {
        $this->server = (new ApplicationFactory($_SERVER))
            ->build(new Url());
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * @param Router $router Already populated Router
     * @param array $options Options for the AspectKernel (passed directly)
     *
     * @Catchable(map={
     *   "RuntimeException": {"severity": "error"},
     *   "OutOfRangeException": {"severity": "error"},
     *   "InvalidArgumentException": {"severity": "error"}
     * })
     *
     * @throws \InvalidArgumentException
     * @throws \Wave\Framework\Exceptions\HttpNotFoundException
     * @throws \Wave\Framework\Exceptions\HttpNotAllowedException
     * @throws \Exception
     */
    public function run(Router $router)
    {
        if (!defined('AOP_CACHE_DIR')) {
            $this->init();
        }
        foreach ($this->middleware as $middleware) {
            $this->server->addMiddleware($middleware);
        }

        $this->server
            ->listen($router);
    }

    /**
     * @return \Wave\Framework\Interfaces\Http\RequestInterface
     */
    public function getRequest()
    {
        return $this->server
            ->getRequest();
    }

    /**
     * @return \Wave\Framework\Interfaces\Http\ResponseInterface
     */
    public function getResponse()
    {
        return $this->server
            ->getResponse();
    }
}