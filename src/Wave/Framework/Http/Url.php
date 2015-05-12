<?php

namespace Wave\Framework\Http;

use Wave\Framework\Interfaces\Http\QueryInterface;
use Wave\Framework\Interfaces\Http\UrlInterface;

class Url implements UrlInterface, \Serializable
{
    const SCHEME_NORM   = 'http';
    const SCHEME_SECURE = 'https';

    private $standardPorts = [
        'https' => 443,
        'http'  => 80
    ];

    protected $scheme    = '';
    protected $host      = '';
    protected $port      = 80;
    protected $path      = '/';
    protected $query     = '';
    protected $fragment  = '';

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

    public function unserialize($data)
    {
        $data = unserialize($data);
        foreach ($data as $key => $value) {
            if (property_exists($this, '_' . strtolower($key))) {
                $this->{'_' . $key} = $value;
            }
        }
    }

    public function __construct(
        $path = '/',
        QueryInterface $query = null,
        $host = null,
        $port = null,
        $scheme = '',
        $fragment = ''
    ) {
        $this->path = $path;
        $this->query = $query;

        $this->host = (string) $host;
        if (1 <= (int) $port && (int) $port <= 65535) {
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

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

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

    public function setHost($host)
    {
        $self = clone $this;
        $self->host = filter_var($host, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);

        return $self;
    }

    public function setPort($port)
    {
        if (!is_int($port)) {
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

    public function setPath($path)
    {
        $self = clone $this;
        $self->path = $path;

        return $self;
    }

    public function setQuery(QueryInterface $query)
    {
        $self = clone $this;
        $self->query = $query;

        return $self;
    }

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
