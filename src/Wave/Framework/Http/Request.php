<?php

namespace Wave\Framework\Http;

use Wave\Framework\Interfaces\Http\RequestInterface;

class Request implements RequestInterface
{
    use Headers {
        setHeader as protected setNewHeader;
        addHeaders as protected addNewHeaders;
        addHeader as protected addNewHeader;
    }

    private $method;
    private $url;
    private $body;


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
     * @see \Wave\Framework\Interfaces\Http\RequestInterface::__construct
     * @param string                                              $method
     * @param string|\Wave\Framework\Interfaces\Http\UrlInterface $uri
     * @param array                                               $headers
     * @param string                                              $body
     */
    public function __construct($method, $uri, array $headers = [], $body = 'php://input')
    {
        if (!$this->isValidMethod($method)) {
            throw new \InvalidArgumentException(sprintf(
                'Method "%s" is not a valid HTTP method',
                $method
            ));
        }
        $this->method = strtoupper($method);

        $this->url = $uri;
        $this->body = file_get_contents($body);
    }

    public function setHeader($header, $value)
    {
        $self = clone $this;
        $self->setNewHeader($header, $value);

        return $self;
    }

    public function addHeader($header, $value)
    {
        $self = clone $this;
        $self->addNewHeader($header, $value);

        return $self;
    }

    public function addHeaders($headers)
    {
        $self = clone $this;
        $self->addNewHeaders($headers);

        return $self;
    }

    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getBody()
    {
        return $this->body;
    }

    private function isValidMethod($method)
    {
        return in_array($method, $this->methods, true);
    }
}
