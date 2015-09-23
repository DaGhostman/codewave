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
use Doctrine\Common\Annotations\AnnotationReader;
use Go\Aop\Intercept\MethodInvocation;
use ReflectionMethod;
use Wave\Framework\Exceptions\AspectAnnotationException;

/**
 * Class AnnotationAspect
 * @package Wave\Framework\Aspects
 */
abstract class AnnotationAspect implements Aspect
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * The annotation used for the current aspect
     *
     * @var string
     */
    protected $annotation;

    /**
     * Instantiates the annotation reader
     */
    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * Returns the default annotation for the $method or
     * the provided $annotation
     *
     * @param $method ReflectionMethod
     * @param $annotation string
     *
     * @throws AspectAnnotationException
     * @return null|object
     */
    public function getMethodAnnotation(ReflectionMethod $method, $annotation = null)
    {
        if ($annotation === null) {
            if ($this->annotation === null) {
                throw new AspectAnnotationException(
                    sprintf('Annotation not provided for aspect: "%s"', get_class($this))
                );
            }

            $annotation = $this->annotation;
        }

        return $this->annotationReader->getMethodAnnotation(
            $method,
            $annotation
        );
    }

    /**
     * Returns all annotations for $method
     *
     * @param $method ReflectionMethod
     * @return array
     */
    public function getMethodAnnotations(ReflectionMethod $method)
    {
        return $this->annotationReader->getMethodAnnotations($method);
    }

    /**
     * Returns the default annotation for the $class or
     * the provided $annotation
     *
     *
     * @param $class string|object The object of which the annotation should be received
     * @param $annotation string The alternative annotation to fetch from the class
     *
     * @throws AspectAnnotationException
     *
     * @return null|object
     */
    public function getClassAnnotation($class, $annotation = null)
    {
        if ($annotation === null) {
            if ($this->annotation === null) {
                throw new AspectAnnotationException(
                    sprintf('Annotation not provided for aspect: "%s"', get_class($this))
                );
            }

            $annotation = $this->annotation;
        }

        if (!$class instanceof \ReflectionClass) {
            $class = new \ReflectionClass($class);
        }

        return $this->annotationReader->getClassAnnotation(
            $class,
            $annotation
        );
    }

    /**
     * Returns all annotations of the $class
     *
     * @param $class string|object The class of which to retrieve the annotations
     *
     * @return array
     */
    public function getClassAnnotations($class)
    {
        if (!$class instanceof \ReflectionClass) {
            $class = new \ReflectionClass($class);
        }

        $this->annotationReader->getClassAnnotations($class);
    }
}
