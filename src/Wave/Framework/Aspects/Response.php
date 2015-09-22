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

use Go\Lang\Annotation\Around;
use Go\Lang\Annotation\Before;
use Go\Aop\Intercept\MethodInvocation;
use Wave\Framework\Http\Response as HttpResponse;

/**
 * Class Response
 * @package Wave\Framework\Aspects
 */
class Response extends AnnotationAspect
{
    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @param HttpResponse $response
     */
    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
        parent::__construct();
    }

    /**
     * @param MethodInvocation $invocation
     *
     * @Around("@annotation(Wave\Framework\Annotations\Http\Response)")
     */
    public function aroundResponseAnnotation(MethodInvocation $invocation)
    {
        /**
         * @var $annotation \Wave\Framework\Annotations\Http\Response
         */
        $annotation = $this->annotationReader->getMethodAnnotation(
            $invocation->getMethod(),
            '\Wave\Framework\Annotations\Http\Response'
        );

        $this->response->setStatus($annotation->getStatus());
        $this->response->addHeaders($annotation->getHeaders());

        ob_start();
        $this->response->setBody($invocation->proceed());
        ob_end_clean(); // Prevents output from the action method (ensures all headers will be accepted)
    }

    /**
     * @param MethodInvocation $invocation
     *
     * @Before("@annotation(Wave\Framework\Annotations\Http\Status)")
     */
    public function beforeStatusAnnotation(MethodInvocation $invocation)
    {
        /**
         * @var $annotation \Wave\Framework\Annotations\Http\Status
         */
        $annotation = $this->annotationReader->getMethodAnnotation(
            $invocation->getMethod(),
            '\Wave\Framework\Annotations\Http\Status'
        );

        $this->response->setStatus($annotation->getStatus());
        $invocation->proceed();
    }


    /**
     * @param MethodInvocation $invocation
     *
     * @Before("@annotation(Wave\Framework\Annotations\Http\Headers)")
     */
    public function beforeHeadersAnnotation(MethodInvocation $invocation)
    {
        /**
         * @var $annotation \Wave\Framework\Annotations\Http\Headers
         */
        $annotation = $this->annotationReader->getMethodAnnotation(
            $invocation->getMethod(),
            '\Wave\Framework\Annotations\Http\Headers'
        );

        $this->response->addHeaders($annotation->getHeaders());
        $invocation->proceed();
    }
}
