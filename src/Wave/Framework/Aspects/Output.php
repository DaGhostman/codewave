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
use Wave\Framework\Interfaces\General\ControllerInterface;

/**
 * Class Output
 * @package Wave\Framework\Aspects
 */
class Output extends AnnotationAspect
{
    /**
     * @var string
     */
    protected $acceptHeaderValue = 'text/html';

    /**
     * @var string
     */
    protected $annotation = '\Wave\Framework\Annotations\Output\Html';

    /**
     * @Around("@annotation(Wave\Framework\Annotations\Output\Html)")
     *
     * @param MethodInvocation $invocation
     * @return null|string
     */
    public function aroundHtmlOutputAnnotation(MethodInvocation $invocation)
    {
        $accepted = [
            'text/html',
            'application/xhtml+xml',
            'application/xml'
        ];

        $object = $invocation->getThis();
        $this->acceptHeaderValue = $object->getRequest()
            ->getHeader('Accept') || 'text/html';

        foreach ($accepted as $value) {
            if (strpos($this->acceptHeaderValue, $value) !== false) {
                header('Content-type: text/html', true);
                $annotation = $this->getMethodAnnotation($invocation->getMethod());


                if (is_null($annotation)) {
                    throw new \RuntimeException(sprintf(
                        'Annotation "Html" not found in class "%s"',
                        get_class($object)
                    ));
                }

                if (!$object instanceof ControllerInterface) {
                    throw new \RuntimeException(
                        'Class "%s" must implement \Wave\Framework\Interfaces\General\ControllerInterface'
                    );
                }

                $templateEngine = $object->getTemplateEngine();
                $data = $invocation->proceed();
                return $templateEngine->render($annotation->getTemplateName(), $data);
                break;
            }
        }

        return $invocation->proceed();
    }


    /**
     * @Around("@annotation(Wave\Framework\Annotations\Output\Json)")
     *
     *
     * @param MethodInvocation $invocation
     * @return mixed|string
     */
    public function aroundJsonOutputAnnotation(MethodInvocation $invocation)
    {
        $accepted = [
            'application/json'
        ];

        $object = $invocation->getThis();
        $this->acceptHeaderValue = $object->getRequest()
                ->getHeader('Accept') || 'text/html';

        foreach ($accepted as $value) {
            if (strpos($this->acceptHeaderValue, $value) !== false) {
                header('Content-type: application/json', true);
                $json = json_encode(
                    $invocation->proceed(),
                    JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_PARTIAL_OUTPUT_ON_ERROR
                );

                if (json_last_error() === JSON_ERROR_NONE) {
                    return $json;
                }

                throw new \RuntimeException(sprintf(
                    'Unable to encode JSON. Error: "%s"',
                    json_last_error_msg()
                ));
            }
        }

        return $invocation->proceed();
    }
}
