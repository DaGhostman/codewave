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
namespace Wave\Framework\Aspects;

use Go\Aop\Aspect;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\Around;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wave\Framework\Exceptions\CsrfTokenException;

/**
 * Class Csrf
 * @package Wave\Framework\Aspects
 */
class Csrf implements Aspect
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Csrf constructor.
     */
    public function __construct()
    {
        $this->token = md5(uniqid(mt_rand(), true));
    }

    /**
     * @param MethodInvocation $invocation
     * @Around("@annotation(Wave\Framework\Annotations\Security\Csrf\Generate)")
     * @return $this
     */
    public function generateCsrfToken(MethodInvocation $invocation)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['_csrf'] = $this->token;


            // Hack to update the instance of the Request object
            $arguments = $invocation->getArguments();
            $ref = new \ReflectionProperty($invocation, 'arguments');
            $ref->setAccessible(true);
            $arguments[0] = $arguments[0]
                ->withAttribute('csrf', $this->token);
            $ref->setValue($invocation, $arguments);
        }

        return $invocation->proceed();
    }

    /**
     * @Around("@annotation(Wave\Framework\Annotations\Security\Csrf\Validate)")
     *
     * @param MethodInvocation $invocation
     *
     * @throws CsrfTokenException When expected and provided token don't match
     * @throws \RuntimeException When the field which holds the token for validation is not found in the request body
     *
     * @return ResponseInterface
     */
    public function validateCsrfToken(MethodInvocation $invocation)
    {
        /**
         * @var $request ServerRequestInterface
         */
        $request = $invocation->getArguments()[0];

        if (array_key_exists('_csrf', $_SESSION)) {
            $field = $invocation->getMethod()
                ->getAnnotation('\Wave\Framework\Annotations\Security\Csrf\Validate');

            $request = $request->withAttribute('csrf', $_SESSION['_csrf']);
            if ($_SESSION['_csrf'] !== $request->getParsedBody()[$field->getField()]) {
                throw new CsrfTokenException('Provided token and registered token do not match');
            }

            unset($_SESSION['_csrf']);
            $response = $invocation->proceed();

            return $response;
        }

        throw new \RuntimeException('No token found for comparison');
    }
}
