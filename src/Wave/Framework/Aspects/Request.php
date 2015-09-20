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
use Go\Lang\Annotation\Before;
use Wave\Framework\Interfaces\General\ControllerInterface;
use Wave\Framework\Interfaces\Http\RequestInterface;

/**
 * Class Request
 * @package Wave\Framework\Aspects
 */
class Request implements Aspect
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param $request RequestInterface
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param MethodInvocation $invocation
     *
     * @Before("@annotation(Wave\Framework\Annotations\Controller\Request)")
     */
    public function beforeRequestAnnotation(MethodInvocation $invocation)
    {
        $object = $invocation->getThis();
        if (!$object instanceof ControllerInterface) {
            throw new \RuntimeException(sprintf(
                'Expected class "%s" to implement Interfaces\General\ControllerInterfaces',
                get_class($object)
            ));
        }

        $object->setRequest($this->request);
    }
}