<?php
namespace Wave\Http;

class Response
{
    protected $headers = array();
    
    public function redirect($location, $permanent = false)
    {
        if (false === $permanent) {
            $this->headers[sprintf('Location: %s', $location)] = 302;
        }
        
        if (true === $permanent) {
                $this->headers[sprintf('Location: %s', $location)] = 301;
        }
        
        return $this;
    }
    
    public function OK()
    {
        $this->headers['200 OK'] = 200;
        
        return $this;
    }
    
    public function halt()
    {
        $this->headers['500 Internal Error'] = 500;
        
        return $this;
    }
    
    public function forbidden()
    {
        $this->headers['403 Forbidden'] = 403;
        
        return $this;
    }
    
    public function notFound()
    {
        $this->headers['404 Not Found'] = 404;
        
        return $this;
    }
    
    public function unauthorized()
    {
        $this->headers['401 Unauthorized'] = 401;
        
        return $this;
    }
    
    public function send()
    {
        if (headers_sent()) {
            return false;
        }
        
        foreach ($this->headers as $header => $status) {
            header($header, true, $status);
        }
    }
}
