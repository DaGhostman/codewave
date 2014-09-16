<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 13:05
 */

namespace Wave\Framework\Storage;


class Cookie
{
    /**
     * @var string
     */
    protected $name = null;
    /**
     * @var string
     */
    protected $value = null;

    protected $options = array(
        'domain' => null,
        'httponly' => true,
        'secure' => false,
        'path' => '/',
        'expiry' => 3600
    );

    /**
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options = array())
    {
        $this->name = $name;

        if (isset($_COOKIE[$name])) {
            $this->value = $_COOKIE[$name];
        }

        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param $domain string Set domain for the cookie
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->options['domain'] = $domain;

        return $this;
    }

    /**
     * Is the current cookie set
     * @return bool
     */
    public function exists()
    {
        return isset($_COOKIE[$this->name]);
    }

    /**
     * @param $path string Set the path parameter of the cookie
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->options['path'] = $path;

        return $this;
    }

    /**
     * @param $secure bool True to set as secure cookie, false otherwise
     * @return $this
     */
    public function setSecure($secure)
    {
        $this->options['secure'] = (bool) $secure;

        return $this;
    }

    /**
     * @param $seconds int Seconds, after which to expire
     *
     * @return $this
     */
    public function setExpiry($seconds)
    {
        $this->options['expiry'] = $seconds;

        return $this;
    }

    /**
     * @param $value mixed The new value to set of the cookie
     */
    public function set($value)
    {
        $this->value = (string) $value;
    }

    public function get()
    {
        return $this->value;
    }

    /**
     * @codeCoverageIgnore
     */
    public function expire()
    {
        setcookie($this->name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    public function __destruct()
    {
        if ((!isset($_COOKIE[$this->name])) || ($_COOKIE[$this->name] != $this->value)) {
            setcookie(
                $this->name,
                $this->value,
                time() + $this->options['expiry'],
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'],
                $this->options['httponly']
            );
        }
    }
}
