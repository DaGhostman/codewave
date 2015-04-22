<?php

namespace Wave\Framework\Http;

class Response
{
    use Headers;

    private $codes = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    protected $status = 200;
    protected $body = '';

    public function __toString()
    {
        $status = $this->getStatus();
        $response = sprintf('HTTP/%s %d %s' . PHP_EOL, $this->getVersion(), $status[0], $status[1]);
        foreach ($this->headers as $header => $value) {
            foreach ($value as $val) {
                $response .= $header . ': ' . $val . PHP_EOL;
            }
        }
        $response .= PHP_EOL . PHP_EOL;
        $response .= (string) $this->body;

        return $response;
    }

    /**
     * Returns numeric array with the current status code and message
     *      0 => the status code
     *      1 => the status message
     *
     * @return array
     */
    public function getStatus()
    {
        return [$this->status, $this->codes[$this->status]];
    }


    public function setStatus($code)
    {
        if (!array_key_exists($code, $this->codes)) {
            throw new \InvalidArgumentException(sprintf(
                'The provided HTTP code "%d" is invalid',
                $code
            ));
        }

        $self = clone $this;
        $self->status = $code;

        return $self;
    }
}
