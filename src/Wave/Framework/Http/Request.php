<?php

namespace Wave\Framework\Http;

use Wave\Framework\Interfaces\Http\QueryInterface;
use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\UrlInterface;

class Request implements RequestInterface
{
    private $method;
    private $url;
    private $body;

    protected $headers = [];
    protected $version = 'HTTP/1.1';


    private $methods = [
        'CONNECT',
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
        'TRACE'
    ];

    /**
     * The constructor of the request object should be responsible
     * for defining all mandatory fields of the request object.
     * The $uri should be either string or object instance implementing
     * the UrlInterface. This is so to ensure compatibility of all internal
     * components which rely on that object, i.e \Wave\Framework\Http\Server
     *
     * @param string              $method  A valid HTTP method verb
     * @param UrlInterface $uri     Object or string
     * @param array               $headers Array of headers to set up on construction
     * @param string              $body    The stream from which to get the HTTP body, defaults to 'php://input'
     */
    public function __construct($method, UrlInterface $uri, array $headers = [], $body = 'php://input')
    {
        $method = strtoupper($method);
        if (!$this->isValidMethod($method)) {
            throw new \InvalidArgumentException(sprintf(
                'Method "%s" is not a valid HTTP method',
                $method
            ));
        }
        $this->method = strtoupper($method);

        foreach ($headers as $header => $value) {
            if (!is_array($value)) {
                $value = [$value];
            }

            $this->headers[$this->parseHeader($header)] = $value;
        }

        $this->url = $uri;

        $this->body = $body;
    }

    /**
     * Factory for the Url object.
     * Although this creates a tight coupling, the URL is
     * vital part of the HTTP request, it seems inappropriate if the
     * construction logic is entirely placed within the Server object,
     * as the server should do the heavy lifting for the
     *
     *
     * @param array $server
     * @param UrlInterface $url
     * @param QueryInterface $query
     * @return Url
     */
    public static function buildUrl(UrlInterface $url, QueryInterface $query, array $server)
    {
        if ($server['SERVER_PORT'] === 443 ||
            (isset($server['HTTPS']) && $server['HTTPS'] !== 'off' && !empty($server['HTTPS']))
        ) {
            $url = $url->setScheme('https');
        }

        return $url->setHost($server['SERVER_NAME'])
            ->setPort((int) $server['SERVER_PORT'])
            ->setPath(parse_url($server['REQUEST_URI'], PHP_URL_PATH))
            ->setQuery($query->import(parse_url($server['REQUEST_URI'], PHP_URL_QUERY)));
    }

    /**
     * Add a header to the current object.
     * This method should have dual behaviour, based on the $append
     * variable. If set to true the method should create/append the new
     * header value or create/overwrite the header's value.
     *
     * In case of representing server requests, this method should return a
     * new instance of the object with the new value added.
     *
     * @param string $header
     * @param string $value
     * @param bool   $append
     *
     * @return mixed
     */
    public function addHeader($header, $value, $append = true)
    {
        $header = $this->parseHeader($header);
        if (!is_array($value)) {
            $value = [$value];
        }

        $self = clone $this;

        if ($append) {
            if ($self->hasHeader($header)) {
                $self->headers[$header] = array_merge($self->headers[$header], $value);
            } else {
                $self->headers[$header] = $value;
            }
        } else {
            $self->headers[$header] = $value;
        }

        return $self;
    }

    /**
     * Same behaviour as addHeader method, but instead of adding single
     * header, it adds multiple headers. Respectively from the key => value
     * pairs in the $headers argument.
     *
     * @see RequestInterface::addHeader
     *
     * @param array $headers
     * @param bool  $append
     *
     * @return mixed
     */
    public function addHeaders(array $headers, $append = true)
    {
        $self = clone $this;
        foreach ($headers as $header => $value) {
            $self = $self->addHeader($header, $value, $append);
        }

        return $self;
    }

    /**
     * Check if a header is defined
     *
     * @param $header string
     *
     * @return bool
     */
    public function hasHeader($header)
    {
        return array_key_exists($this->parseHeader($header), $this->headers);
    }

    /**
     * Return the value of a header identified by $header.
     * If the header does not exist it is recommended to be handled
     * gracefully, i.e no need to throw an exception as it is acceptable
     * for a client to not send a given header. Exception throwing should
     * come from the application if the headers if required for its normal
     * operation.
     *
     * @param $header string name of the headers to retrieve
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
     * Return the HTTP request method verb with which the request was performed
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return the current URL as object.
     *
     * @see UrlInterface
     *
     * @return UrlInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return the raw request body of the request.
     * Handle with care as request body could be memory consuming
     * in case of file uploads for example.
     *
     * @return string|null
     */
    public function getBody()
    {
        return file_get_contents($this->body);
    }

    private function isValidMethod($method)
    {
        return in_array($method, $this->methods, true);
    }

    private function parseHeader($header)
    {
        $header = str_replace('-', ' ', $header);
        $header = ucwords(strtolower($header));
        return str_replace(' ', '-', $header);
    }

    /**
     * Return all headers which are defined in the current request object
     *
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
