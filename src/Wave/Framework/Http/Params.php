<?php

namespace Wave\Framework\Http;

trait Params
{
    protected $params = [];
    public function withParams(array $params)
    {
        $new = clone $this;
        $new->params = $params;

        return $new;
    }

    public function withParam($key, $value)
    {
        $new = clone $this;
        $new->params[$key] = $value;

        return $new;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function hasParam($param)
    {
        return array_key_exists($param, $this->params);
    }

    public function __get($key)
    {
        if (!$this->hasParam($key)) {
            return null;
        }

        return $this->params[$key];
    }
}
