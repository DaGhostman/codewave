<?php
namespace Wave\Framework\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{

    protected $scheme = '';

    protected $user = '';

    protected $host = '';

    protected $port = 0;

    protected $path = '';

    protected $query = '';

    protected $fragment = '';

    /**
     * Uses the passed argument to get all of the
     * URL elements
     *
     * @param null|string|object $uri
     */
    public function __construct($uri = '/')
    {
        if (! is_string($uri)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected string, got: ',
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }

        if (! empty($uri)) {
            foreach (parse_url($uri) as $key => $value) {
                if ($key == 'pass') {
                    $this->user .= ':' . $value;
                    continue;
                }

                $this->$key = $value;
            }
        }
    }

    /**
     * @see UriInterface::getScheme()
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @see UriInterface::getAuthority()
     * @return string
     */
    public function getAuthority()
    {
        if (empty($this->host)) {
            return '';
        }

        $authority = $this->host;

        if (! empty($this->user)) {
            $authority = $this->user . '@' . $this->host;
        }

        if (80 !== (int) $this->port || 443 !== (int) $this->port) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * @see UriInterface::getUserInfo()
     * @return string
     */
    public function getUserInfo()
    {
        return $this->user;
    }

    /**
     * @see UriInterface::getHost()
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @see UriInterface::getPort()
     * @return int
     */
    public function getPort()
    {
        return (int) $this->port;
    }

    /**
     * @see UriInterface::getPath()
     * @return string
     */
    public function getPath()
    {
        return ($this->path ?  : '/');
    }

    /**
     * @see UriInterface::getQuery()
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @see UriInterface::getFragment()
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @see UriInterface::withScheme()
     * @param string $scheme Scheme of the current URI
     * @return UriInterface
     */
    public function withScheme($scheme)
    {
        $scheme = strtolower($scheme);
        if (substr($scheme, 0, - 3)) {
            $scheme = substr($scheme, 0, strlen($scheme) - 3);
        }

        if (! in_array($scheme, [
            '',
            'http',
            'https'
        ], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid scheme supplied \'%s\'', $this->scheme));
        }

        $new = clone $this;
        $new->scheme = $scheme;

        return $new;
    }

    /**
     * @see UriInterface::withUserInfo()
     *
     * @param string $user
     * @param string|null $password
     * @return UriInterface
     */
    public function withUserInfo($user, $password = null)
    {
        $user = $user . ($password ? ':' . $password : '');

        $new = clone $this;
        $new->user = $user;

        return $new;
    }

    /**
     * @see UriInterface::withHost()
     *
     * @param string $host
     * @return UriInterface
     */
    public function withHost($host)
    {
        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * @see UriInterface::withPort()
     *
     * @param string|int $port
     * @return UriInterface
     */
    public function withPort($port)
    {
        // is_float is not the best opinion, but just in case :P
        if (! is_numeric($port) || is_float($port)) {
            throw new \InvalidArgumentException('Supplied port should be an integer');
        }

        if (1 > $port && 65535 < $port) {
            throw new \OutOfRangeException('Port value out of range, valid range is 1, 65535.');
        }

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * @see UriInterface::withPath()
     *
     * @param string $path
     * @return UriInterface
     */
    public function withPath($path)
    {
        if (! is_string($path)) {
            throw new \InvalidArgumentException('Expected string');
        }

        if (strpos($path, '?') || strpos($path, '#')) {
            throw new \InvalidArgumentException('Path may not contain \'?\' or \'#\'.');
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * @see UriInterface::withQuery()
     *
     * @param string $query
     * @return UriInterface
     */
    public function withQuery($query)
    {
        if (! is_string($query)) {
            throw new \InvalidArgumentException('Query must be a string');
        }

        if (strpos($query, '#')) {
            throw new \InvalidArgumentException('Query may not contain \'#\'.');
        }

        if (strpos($query, '?') === 0) {
            $query = substr($query, 1);
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * @see UriInterface::withFragment()
     *
     * @param string $fragment
     * @return UriInterface
     */
    public function withFragment($fragment)
    {
        if (strpos($fragment, '#') === 0) {
            $fragment = substr($fragment, 1);
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $uri = '';
        if (! empty($this->scheme)) {
            $uri .= sprintf('%s://', $this->getScheme());
        }

        if ($this->getAuthority() != '' && $this->getAuthority() != null) {
            $uri .= $this->getAuthority();
        }

        if ($this->path) {
            $uri .= $this->getPath();
        }

        if ($this->query) {
            $uri .= sprintf('?%s', $this->getQuery());
        }

        if ($this->fragment) {
            $uri .= sprintf('#%s', $this->getFragment());
        }

        return $uri;
    }
}
