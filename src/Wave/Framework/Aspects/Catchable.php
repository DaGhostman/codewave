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
use Psr\Log\LoggerInterface;

/**
 * Class Catchable
 * @package Wave\Framework\Aspects
 */
class Catchable extends AnnotationAspect
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * @param MethodInvocation $invocation
     *
     * @Around("@annotation(Wave\Framework\Annotations\General\Catchable)")
     * @throws \Exception
     * @return mixed
     */
    public function aroundCatchableAnnotation(MethodInvocation $invocation)
    {
        try {
            return $invocation->proceed();
        } catch (\Exception $ex) {
            /**
             * @var $annotation \Wave\Framework\Annotations\General\Catchable
             */
            $annotation = $this->annotationReader->getMethodAnnotation(
                $invocation->getMethod(),
                '\Wave\Framework\Annotations\General\Catchable'
            );

            $this->callLogger($ex, $annotation, $invocation);
        }
    }

    /**
     * @param MethodInvocation $invocation
     *
     * @Around("@within(Wave\Framework\Annotations\General\Catchable)")
     * @throws \Exception
     * @return mixed
     */
    public function aroundCatchableClassAnnotation($invocation)
    {
        try {
            return $invocation->proceed();
        } catch (\Exception $ex) {
            $annotation = $this->annotationReader->getClassAnnotation(
                new \ReflectionClass($invocation->getThis()),
                '\Wave\Framework\Annotations\General\Catchable'
            );

            $this->callLogger($ex, $annotation, $invocation);
        }
    }

    private function callLogger($ex, $annotation, $invocation)
    {
        /**
         * @var $exception \Wave\Framework\Annotations\General\Exception|null
         */
        $exception = $annotation->getException(get_class($ex));

        $severity = 'CRITICAL';
        $message = $message = 'Call of {class}:{method} resulted in "{exception}" ' .
            'exception with "message" {message} @ {file}:{line} ' . PHP_EOL .
            'Arguments: {arguments}';
        $rethrow = true;

        if ($exception !== null) {
            $severity = strtolower($exception->getSeverity());
            $message = $exception->getMessage();
            $rethrow = $exception->getRethrow();
        }

        call_user_func([$this->logger, $severity], $message, [
            'class' => get_class($invocation->getThis()),
            'method' => $invocation->getMethod()->name,
            'exception' => get_class($ex),
            'message' => $ex->getMessage() ,
            'file' => $ex->getFile(),
            'line' => $ex->getLine(),
            'arguments' => $invocation->getArguments()
        ]);

        if ($rethrow === true) {
            throw $ex;
        }
    }
}
