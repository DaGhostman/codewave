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
namespace Wave\Framework\Middleware;

use Exceptions\Dispatch\UnauthorizedException;
use Wave\Framework\Annotations\General\Catchable;
use Wave\Framework\Application\Router;
use Wave\Framework\Exceptions\Dispatch\MethodNotAllowedException;
use Wave\Framework\Exceptions\Dispatch\NotAcceptableException;
use Wave\Framework\Exceptions\Dispatch\NotFoundException;
use Wave\Framework\Exceptions\HttpNotAllowedException;
use Wave\Framework\Exceptions\HttpNotFoundException;
use Wave\Framework\Interfaces\Middleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\MessageInterface;
use Zend\Diactoros\Stream;

/**
 * Class RouterMiddleware
 * @package Wave\Framework\Middleware
 *
 * @internal
 *
 * Middleware that handles the routing and some bits and
 * pieces of the error handling, like setting the appropriate
 * header values when an error occurs.
 */
class RouterMiddleware implements MiddlewareInterface
{

    /**
     * @var Router
     */
    protected $router;

    /**
     * RouterMiddleware constructor.
     * Injects the already populated router object to be used by
     * the middleware for routing.
     * @internal
     * @param Router $router The router with the defined parameters
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * The callback which is used to dispatch the specific route,
     * should not be used directly in any case it is strictly
     * intended to be invoked when the middleware's are dispatched
     *
     * @internal
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param MiddlewareInterface|callable|null $next
     *
     * @throws \InvalidArgumentException
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next = null)
    {
        if ($next !== null) {
            $response = $next($request, $response) ?: $response;
        }

        try {
            if ($request->getMethod() !== 'TRACE') {
                // TRACE should only output request
                $response = $this->router->dispatch(
                    $request,
                    $response
                );
            }

            if ($request->getMethod() === 'HEAD') {
                // Prevent servers which do not remove the body
                // of the responses, when receiving HEAD requests
                // to violate the expected behaviour
                $response = $response->withBody(new Stream(''));
            }
        } catch (NotFoundException $ex) {
            $response = $response->withStatus(404);
        } catch (MethodNotAllowedException $ex) {
            $response = $response->withStatus(405)
                ->withAddedHeader('Allow', implode(', ', $ex->getAllowed()));
        } catch (\Exception $ex) {
            $response = $response->withStatus(500);
        }

        return $response;
    }
}
