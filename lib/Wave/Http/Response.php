<?php
namespace Wave\Http;

class Response
{

    protected $headers = array();
    protected $protocol = "HTTP/1.1";

    
    private $statusCodes = array(
        200, 301, 302, 401, 403, 404, 500
    );
    
    /**
     * Redirects the client to the location.
     *
     * @param string $location
     *            The location of the redirect
     * @param string $permanent
     *            True = 301 redirect, false = 302
     * @return \Wave\Http\Response
     */
    public function redirect($location, $permanent = false)
    {
        if (false === $permanent) {
            $this->headers[302] = sprintf('Location: %s', $location);
        }
        
        if (true === $permanent) {
            $this->headers[301] = sprintf('Location: %s', $location);
        }
        
        return $this;
    }

    /**
     * Sends the 200 OK status
     *
     * @return \Wave\Http\Response
     */
    public function OK()
    {
        $this->headers[200] = sprintf('%s 200 OK', $this->protocol);
        
        return $this;
    }

    /**
     * Sends the 500 Internal Error status
     *
     * @return \Wave\Http\Response
     */
    public function halt()
    {
        $this->headers[500] =
            sprintf('%s 500 Internal Error', $this->protocol);
        
        return $this;
    }

    /**
     * Sends the 403 Forbidden status
     *
     * @return \Wave\Http\Response
     */
    public function forbidden()
    {
        $this->headers[403] = sprintf('%s 403 Forbidden', $this->protocol);
        
        return $this;
    }

    /**
     * Sends the 404 Not Found status
     *
     * @return \Wave\Http\Response
     */
    public function notFound()
    {
        $this->headers[404] = sprintf('%s 404 Not Found', $this->protocol);
        
        return $this;
    }

    /**
     * Sends the 401 Unauthorized status
     *
     * @return \Wave\Http\Response
     */
    public function unauthorized()
    {
        $this->headers[401] = sprintf('%s 401 Unauthorized', $this->protocol);
        
        return $this;
    }

    /**
     * Send the headers to the client.
     *
     * @return boolean false if headers are already sent
     */
    public function send()
    {
        if (headers_sent()) {
            return false;
        }
        
        // @codeCoverageIgnoreStart
        foreach ($this->headers as $status => $header) {
            if (in_array($status, $this->statusCodes)) {
                header($header, true, $status);
                continue;
            }
            
            header($header);
        }
    }
    // @codeCoverageIgnoreEnd
}
