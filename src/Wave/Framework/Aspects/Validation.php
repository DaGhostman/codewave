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
use Go\Lang\Annotation\Before;
use Wave\Framework\Annotations\General\ParameterValidator;

class Validation extends AnnotationAspect
{

    protected $annotation = '\Wave\Framework\Annotations\General\ParameterValidator';

    /**
     * @param MethodInvocation $invocation
     * @throws \Wave\Framework\Exceptions\AspectAnnotationException
     * @throws \InvalidArgumentException
     *
     * @Before("@annotation(Wave\Framework\Annotations\General\ParameterValidator)")
     */
    public function beforeValidationAnnotation(MethodInvocation $invocation)
    {
        /**
         * @var $annotation ParameterValidator
         */
        $args = $invocation->getArguments();
        $annotation = $this->getMethodAnnotation($invocation->getMethod());
        foreach ($annotation->getPatterns() as $index => $regex) {
            if (array_key_exists($index, $args)) {
                if (preg_match($regex, $args[$index]) !== 1) {
                    throw new \InvalidArgumentException(sprintf(
                        'Parameter at position "%d"(\'%s\') does not match the required pattern in %s::%s',
                        $index,
                        $args[$index],
                        get_class($invocation->getThis()),
                        $invocation->getMethod()->name
                    ));
                }
            }
        }
    }
}
