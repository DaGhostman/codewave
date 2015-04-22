<?php

namespace Http\Http\Entities\Header;

class Cookie
{
    private $parts = [];
    public function __construct(
        $name,
        $value,
        $ttl = null,
        $path = null,
        $domain = null,
        $secure = null,
        $httpOnly = true
    ) {
        $this->parts['name'] = $name;
        $this->parts['value'] = $value;
        $this->parts['ttl'] = ($ttl ? (new \DateTime())->add(
            new \DateInterval('PT' . $ttl . 'S')
        )->format(\DateTime::COOKIE) : null);
        $this->parts['path'] = $path;
        $this->parts['domain'] = $domain;
        $this->parts['secure'] = $secure;
        $this->parts['httpOnly'] = $httpOnly;
    }

    public function getName()
    {
        return $this->parts['name'];
    }

    public function getValue()
    {
        return $this->parts['value'];
    }

    public function __toString()
    {
        $format = 'name=value;';
        if ($this->parts['domain'] !== null) {
            $format .= 'Domain=domain;';
        }
        if ($this->parts['path'] !== null) {
            $format .= 'Path=path;';
        }
        if ($this->parts['ttl'] !== null) {
            $format .= 'Expires=ttl;';
        }
        if ($this->parts['secure']) {
            $format .= 'Secure;';
        }
        if ($this->parts['httpOnly']) {
            $format .= 'HttpOnly';
        }

        return rtrim(strtr($format, $this->parts), ';');
    }
}
