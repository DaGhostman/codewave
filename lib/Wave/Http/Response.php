<?php
namespace Wave\Http;

class Response
{

    protected $headers = array();

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
            $this->headers[sprintf('Location: %s', $location)] = 302;
        }
        
        if (true === $permanent) {
            $this->headers[sprintf('Location: %s', $location)] = 301;
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
        $this->headers['200 OK'] = 200;
        
        return $this;
    }

    /**
     * Sends the 500 Internal Error status
     *
     * @return \Wave\Http\Response
     */
    public function halt()
    {
        $this->headers['500 Internal Error'] = 500;
        
        return $this;
    }

    /**
     * Sends the 403 Forbidden status
     *
     * @return \Wave\Http\Response
     */
    public function forbidden()
    {
        $this->headers['403 Forbidden'] = 403;
        
        return $this;
    }

    /**
     * Sends the 404 Not Found status
     *
     * @return \Wave\Http\Response
     */
    public function notFound()
    {
        $this->headers['404 Not Found'] = 404;
        
        return $this;
    }

    /**
     * Sends the 401 Unauthorized status
     *
     * @return \Wave\Http\Response
     */
    public function unauthorized()
    {
        $this->headers['401 Unauthorized'] = 401;
        
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
        foreach ($this->headers as $header => $status) {
            header($header, true, $status);
        }
    }
}
// @codeCoverageIgnoreEnd
