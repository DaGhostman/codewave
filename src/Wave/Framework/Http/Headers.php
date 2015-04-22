<?php

namespace Wave\Framework\Http;

trait Headers
{

    protected $headers = [];
    protected $version = '1.0';

    /**
     * @param $header string
     * @return string
     */
    private function parseHeaders($header)
    {
        $header = str_replace('-', ' ', $header);
        $header = ucwords($header);

        return str_replace(' ', '-', $header);
    }

    /**
     * Returns a header if exists, false otherwise
     *
     * @param $header string
     *
     * @return string
     */
    public function getHeader($header)
    {
        if ($this->hasHeader($header)) {
            return $this->headers[$this->parseHeaders($header)];
        }

        return null;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param $header string
     * @return bool
     */
    public function hasHeader($header)
    {
        return array_key_exists($this->parseHeaders($header), $this->headers);
    }

    /**
     * Creates the header if it does not exist,
     * otherwise appends the value to it.
     *
     * @param $header
     * @param $value
     * @return \Wave\Framework\Http\Headers
     */
    public function addHeader($header, $value)
    {
        if ($this->hasHeader($header)) {
            $this->headers[$this->parseHeaders($header)][] = $value;
        } else {
            $this->setHeader($header, $value);
        }

        return $this;
    }

    public function addHeaders($headers)
    {
        foreach ($headers as $header => $value) {
            $this->headers[$this->parseHeaders($header)] = $value;
        }

        return $this;
    }

    public function setHeader($header, $value)
    {
        $this->headers[$this->parseHeaders($header)] = [$value];

        return $this;
    }

    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }
}
