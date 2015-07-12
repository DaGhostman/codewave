<?php
namespace Stub;

use Wave\Framework\Interfaces\Http\QueryInterface;
use Wave\Framework\Interfaces\Http\UrlInterface;

class MockUrl implements UrlInterface
{
    private $scheme;
    private $host;
    private $port;
    private $path;
    private $query;
    private $fragment;

    /**
     * Adds all mandatory fields for the URL, the only needed part is $path,
     * as it is always present, others can be safely omitted, although $path
     * can be set to default to '/' it is not recommended to make the code
     * more self-explanatory.
     *
     * @param string         $path   The path of the request
     * @param QueryInterface $query  Object representing the current query
     * @param string         $host   the host name to use in the URL
     * @param int            $port   A valid port number from 1 - 65535
     * @param string         $scheme the scheme of the request (in case of 'file://' urls,
     *                               make sure to use a standard http port [80|443])
     * @param string         $fragment
     */
    public function __construct ($path = '/', QueryInterface $query = null, $host = '', $port = 80, $scheme = 'http', $fragment = null)
    {
        $this->scheme = $scheme;
        $this->path = $path;
        $this->host = $host;
        $this->port = $port;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    /**
     * @return string|null
     */
    public function getScheme ()
    {
        return $this->scheme;
    }

    /**
     * @return string|null
     */
    public function getHost ()
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort ()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath ()
    {
        return $this->path;
    }

    /**
     * @return QueryInterface
     */
    public function getQuery ()
    {
        return $this->query;
    }

    /**
     * @return string|null
     */
    public function getFragment ()
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     *
     * @return mixed
     */
    public function setScheme ($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return mixed
     */
    public function setHost ($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param int $port
     *
     * @return mixed
     */
    public function setPort ($port)
    {
        $this->port = (int) $port;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function setPath ($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param QueryInterface $query
     *
     * @return mixed
     */
    public function setQuery (QueryInterface $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $fragment
     *
     * @return mixed
     */
    public function setFragment ($fragment)
    {
        $this->fragment = $fragment;
    }

    /**
     * @return string
     */
    public function __toString ()
    {
        return $this->scheme . '://' . $this->host . ':' . $this->port . $this->path . ($this->query ? '?' . $this->query : '');
    }
}