<?php
/**
 * Created by PhpStorm.
 * User: ddimitrov
 * Date: 18/11/15
 * Time: 00:13
 */

namespace Wave\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Wave\Framework\Flash\Manager;
use Wave\Framework\Interfaces\Middleware\MiddlewareInterface;

/**
 * Class FlashMessages
 * @package Wave\Framework\Middleware
 */
class FlashMessages implements MiddlewareInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return bool
     */
    public function __invoke($request, $response, $next = null)
    {
        $messages = [];
        if (array_key_exists('_flash', $_SESSION)) {
            $messages = $_SESSION['_flash'];
        }

        $request = $request->withAttribute('flash', new Manager($messages));

        return $next($request, $response);
    }
}
