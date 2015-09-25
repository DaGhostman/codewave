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
namespace Wave\Framework\Annotations\General;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Catchable
 * @package Wave\Framework\Annotations\General
 *
 * @Annotation
 * @Annotation\Target({"METHOD", "CLASS"})
 */
class Exception extends Annotation
{
    /**
     * @type string
     * @Enum({"DEBUG", "INFO", "NOTICE", "WARNING", "ERROR", "CRITICAL", "ALERT", "EMERGENCY"})
     */
    public $severity = 'CRITICAL';

    /**
     * @type string
     */
    public $message;

    /**
     * @type bool
     */
    public $rethrow = true;

    /**
     * Returns, if provided, the severity of this kind of exception.
     * The severity is one of the public method names of the LoggerInterface
     *
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * Returns a message to be used when logging the exception,
     * does NOT replace the exception message, but rather
     * adds formatting to it.
     *
     * @return string
     */
    public function getMessage()
    {
        if ($this->message === null) {
            $this->message = 'Call of {class}:{method} resulted in "{exception}" ' .
                'exception with "message" {message} @ {file}:{line} ' . PHP_EOL .
                'Arguments: {arguments}';
        }

        return $this->message;
    }

    /**
     * Returns whether the exception should be rethrown
     *
     * @return bool
     */
    public function getRethrow()
    {
        return $this->rethrow;
    }
}
