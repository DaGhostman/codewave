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

use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\Around;

/**
 * Class Template
 * @package Wave\Framework\Aspects
 */
class Template extends AnnotationAspect
{
    /**
     * @var object
     */
    private $container;

    /**
     * @param $container object
     */
    public function __construct($container)
    {
        $this->container = $container;
        parent::__construct();
    }

    /**
     * @param MethodInvocation $invocation
     *
     * @Around("@annotation(Wave\Framework\Annotations\General\Template)")
     * @return mixed
     */
    public function aroundTemplateAnnotation(MethodInvocation $invocation)
    {
        $data = $invocation->proceed();
        $annotation = $this->annotationReader->getMethodAnnotation(
            $invocation->getMethod(),
            '\Wave\Framework\Annotations\General\Template'
        );

        $view = $annotation->getView()[0];
        $method=$annotation->getView()[1];

        $viewObject = $this->container
            ->get($view);

        $viewObject->$method($data)
            ->render();

        return $data;
    }
}