<?php

namespace Wave\Framework\Http;

use Wave\Framework\Interfaces\Http\QueryInterface;
use Wave\Framework\Interfaces\Http\UrlInterface;

/**
 * Class Url
 * @package Wave\Framework\Http
 */
class Url implements UrlInterface, \Serializable
{
    const SCHEME_NORM   = 'http';
    const SCHEME_SECURE = 'https';

    private $standardPorts = [
        'https' => 443,
        'http'  => 80
    ];

    private $scheme    = '';
    private $host      = '';
    private $port      = 80;
    private $path      = '/';
    private $query     = '';
    private $fragment  = '';

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize([
            'scheme' => $this->scheme,
            'host' => $this->host,
            'port' => $this->port,
            'path' => $this->path,
            'query' => $this->query,
            'fragment' => $this->fragment
        ]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $data
     * @internal param string $serialized <p>
     * The string representation of the object.
     * </p>
     */
    public function unserialize($data)
    {
        $data = unserialize($data);
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Adds all mandatory fields for the URL, the only needed part is $path,
     * as it is always present, others can be safely omitted, although $path
     * can be set to default to '/' it is not recommended to make the code
     * more self-explanatory.
     *
     * @param string $path The path of the request
     * @param QueryInterface $query Object representing the current query
     * @param string $host the host name to use in the URL
     * @param int $port A valid port number from 1 - 65535
     * @param string $scheme the scheme of the request (in case of 'file://' urls,
     *                       make sure to use a standard http port [80|443])
     * @param string $fragment
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function __construct(
        $path = '/',
        QueryInterface $query = null,
        $host = null,
        $port = null,
        $scheme = '',
        $fragment = null
    ) {
        $this->path = $path;
        $this->query = $query;

        $this->host = (string) $host;

        if (!is_int($port) && null !== $port) {
            throw new \InvalidArgumentException(
                'Supplied port argument must be an integer'
            );
        }
        if (1 <= $port && $port <= 65535) {
            $this->port = (int) $port;
        } else {
            if ($port !== null) {
                throw new \OutOfRangeException(
                    'The provided port is not in the valid port range 1 - 65535'
                );
            }
        }

        if ($this->isStandardPort($this->port)) {
            $this->scheme = array_search($this->port, $this->standardPorts, true);
        } else {
            $scheme = strtolower($scheme);
            if ($scheme === self::SCHEME_NORM || $scheme === self::SCHEME_SECURE || $scheme === '') {
                $this->scheme = $scheme;
            } else {
                throw new \InvalidArgumentException(
                    sprintf('Invalid scheme "%s" provided, valid schemes are "http" or "https"', $scheme)
                );
            }
        }

        $this->fragment = $fragment;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $format = '';
        if ($this->host !== '' && $this->host !== null) {
            $format .= '{scheme}://{host}';
            if (!$this->isStandardPort($this->port)) {
                $format .= ':{port}';
            }
        }
        $format .= '/{path}';

        if ($this->query !== null && count($this->query) > 0) {
            $format .= '?{query}';
        }

        if ($this->fragment !== '') {
            $format .= '#{fragment}';
        }

        return strtr($format, [
            '{scheme}' => $this->scheme,
            '{host}' => $this->host,
            '{port}' => $this->port,
            '{path}' => ltrim($this->path, '/'),
            '{query}' => $this->query,
            '{fragment}' => $this->fragment
        ]);
    }

    /**
     * @return string|null
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string|null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return QueryInterface
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string|null
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function setScheme($scheme)
    {
        if (!in_array(strtolower($scheme), [self::SCHEME_SECURE, self::SCHEME_NORM, ''], true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid scheme provided. Expected "https", "http" or ""(empty string) "%s" received',
                strtolower($scheme)
            ));
        }

        $self = clone $this;
        $self->scheme = strtolower($scheme);

        return $self;
    }

    /**
     * @param string $host
     *
     * @return mixed
     */
    public function setHost($host)
    {
        $self = clone $this;
        $self->host = filter_var($host, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        return $self;
    }

    /**
     * @param int $port
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     *
     * @return mixed
     */
    public function setPort($port)
    {
        if (!is_int($port) && null !== $port) {
            throw new \InvalidArgumentException(
                'Supplied port argument must be an integer'
            );
        }

        if ((int) $port < 1 || (int) $port > 65535) {
            throw new \OutOfRangeException(
                'Port should be in range between 1 - 65535'
            );
        }


        $self = clone $this;
        $self->port = (int) $port;

        return $self;
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function setPath($path)
    {
        $self = clone $this;
        $self->path = $path;

        return $self;
    }

    /**
     * @param QueryInterface $query
     *
     * @return mixed
     */
    public function setQuery(QueryInterface $query)
    {
        $self = clone $this;
        $self->query = $query;

        return $self;
    }

    /**
     * @param string $fragment
     *
     * @return mixed
     */
    public function setFragment($fragment)
    {
        $self = clone $this;
        $self->fragment = $fragment;

        return $self;
    }

    /**
     * Checks if the request is made over HTTPS
     *
     * @return bool
     */
    public function isHttps()
    {
        return ($this->scheme === self::SCHEME_SECURE);
    }

    /**
     * Checks to determine if a port number is a standard
     * port, i.e 80 or 443
     *
     * @param $port
     *
     * @return bool
     */
    private function isStandardPort($port)
    {
        return in_array($port, $this->standardPorts, true);
    }
}
