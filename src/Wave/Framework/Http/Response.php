<?php

namespace Wave\Framework\Http;

use Wave\Framework\Interfaces\Http\ResponseInterface;

class Response implements ResponseInterface
{
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
        511 => 'Network Authentication Required'
    ];

    protected $version = 1.0;
    protected $status = 200;
    protected $headers = [];
    protected $body = '';

    /**
     * Constructs an object and populates it with the headers
     * provided in $headers
     *
     * @param array $headers list of headers
     */
    public function __construct(array $headers = [])
    {
        if (0 !== count($headers)) {
            $this->addHeaders($headers);
        }
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

    /**
     * Set the status code of the response
     *
     * @param int $code
     *
     * @return mixed
     */
    public function setStatus($code)
    {
        if (!array_key_exists($code, $this->codes)) {
            throw new \InvalidArgumentException(sprintf(
                'The provided HTTP code "%d" is invalid',
                $code
            ));
        }

        $this->status = $code;
        return $this;
    }

    /**
     * Add a header to the current object.
     * This method should have dual behaviour, based on the $append
     * variable. If set to true the method should create/append the new
     * header value or create/overwrite the header's value.
     *
     * @param string $header
     * @param string|array $value
     * @param bool   $append
     *
     * @return mixed
     */
    public function addHeader($header, $value, $append = true)
    {
        $header = $this->parseHeader($header);
        /** @noinspection ArrayCastingEquivalentInspection */
        if (!is_array($value)) {
            $value = [$value];
        }

        if ($append) {
            if ($this->hasHeader($header)) {
                $this->headers[$header] = array_merge($this->headers[$header], $value);
            } else {
                $this->headers[$header] = $value;
            }
        } else {
            $this->headers[$header] = $value;
        }

        return $this;
    }

    /**
     * Same behaviour as addHeader method, but instead of adding single
     * header, it adds multiple headers. Respectively from the key => value
     * pairs in the $headers argument.
     *
     * @see ResponseInterface::addHeader
     *
     * @param array $headers
     * @param bool  $append
     *
     * @return mixed
     */
    public function addHeaders(array $headers, $append = true)
    {
        foreach ($headers as $header => $value) {
            $this->addHeader($header, $value, $append);
        }

        return $this;
    }

    /**
     * Return the value of a header identified by $header.
     * If the header does not exist it is recommended to be handled
     * gracefully, i.e no need to throw an exception as it is acceptable
     * for a client to not send a given header. Exception throwing should
     * come from the application if the headers if required for its normal
     * operation.
     *
     * @param string $header
     *
     * @return mixed
     */
    public function getHeader($header)
    {
        if ($this->hasHeader($header)) {
            if (count($this->headers[$this->parseHeader($header)]) > 1) {
                return $this->headers[$this->parseHeader($header)];
            }

            return $this->headers[$this->parseHeader($header)][0];
        }

        return null;
    }

    /**
     * Retrieve all headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Check if a header exists in the current set of headers
     *
     * @param string $header
     *
     * @return mixed
     */
    public function hasHeader($header)
    {
        return array_key_exists($this->parseHeader($header), $this->headers);
    }

    /**
     * Performs necessary transformation on header names to make them valid HTTP header names
     *
     * @param $header
     *
     * @return mixed
     */
    private function parseHeader($header)
    {
        $header = str_replace('-', ' ', $header);
        $header = ucwords(strtolower($header));
        return str_replace(' ', '-', $header);
    }

    /**
     * Sets the HTTP version to use
     *
     * @param float $version
     * @return void
     */
    public function setVersion($version)
    {
        if (!is_float($version)) {
            throw new \InvalidArgumentException('HTTP version for response should be float');
        }
        $this->version = $version;
    }

    /**
     * Returns the HTTP version of the response
     *
     * @return float
     */
    public function getVersion()
    {
        return (float) $this->version;
    }

    /**
     * Retrieve the contents of the response body.
     *
     * At the current implementation this defaults to php://memory,
     * which is where the server writes all data
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}
