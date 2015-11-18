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

use Wave\Framework\Interfaces\Middleware\MiddlewareInterface;
use Wave\Framework\Middleware\RouterMiddleware;
use Zend\Diactoros\Server;

/**
 * Class Core
 * @package Wave\Framework\Application
 */
class Core extends AspectsKernel
{
    /**
     * Config options
     * @var array
     */
    private $config = [
        'debug' => false,
        'allowDirectOutput' => false
    ];

    /**
     * @var MiddlewareInterface[]
     */
    private $middleware = [];

    /**
     * Prepares the core HTTP objects
     * @param $options array
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function __construct(array $options = [])
    {
        $this->config = array_merge($this->config, $options);
        if (array_key_exists('aspects', $this->config) && $this->config['aspects'] === true) {
            self::$instance = $this;
            $this->init($this->config);
        }
    }

    /**
     * @param callable $middleware
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function addMiddleware(callable $middleware)
    {
        if (!is_callable($middleware)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected callable, received %s',
                gettype($middleware)
            ));
        }

        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * @param Router $router Already populated Router
     *
     * @throws \RuntimeException On stream errors
     * @throws \Wave\Framework\Exceptions\Dispatch\MethodNotAllowedException
     * @throws \Wave\Framework\Exceptions\Dispatch\NotFoundException
     * @throws \Exception
     */
    public function run(Router $router)
    {
        $middleware = new RouterMiddleware($router);
        $middleware->setErrorHandler($router->getErrorHandler())
            ->setNotFound($router->getNotFoundHandler());

        $this->middleware = array_merge([$middleware], $this->middleware);

        $next = null;
        $stack = new \SplDoublyLinkedList();
        foreach ($this->middleware as $callback) {
            if ($stack->count() > 0) {
                $next = $stack->top();
            }
            $stack->push(function ($request, $response) use ($callback, $next) {
                return call_user_func($callback, $request, $response, $next);
            });
        }

        $server = Server::createServer(function ($request, $response) use ($stack) {
            /**
             * @var $request \Zend\Diactoros\Request
             * @var $response \Zend\Diactoros\Response
             * @var $stream \Psr\Http\Message\StreamInterface
             */
            if ($request->getMethod() === 'TRACE') {
                $stream = $response->getBody();
                $stream->write(sprintf(
                    '%s %s' . PHP_EOL,
                    $request->getMethod(),
                    $request->getUri()->getPath()
                ));
                foreach ($request->getHeaders() as $header => $values) {
                    foreach ($values as $value) {
                        $stream->write(sprintf('%s: %s' . PHP_EOL, $header, $value));
                    }
                }
            }

            $stack->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO);
            if ($stack !== null && $request->getMethod() !== 'TRACE') {
                $response = call_user_func($stack->top(), $request, $response);
            }

            return $response;
        }, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $server->listen();
    }
}
