<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 24/08/14
 * Time: 00:17
 */

namespace Wave\Framework\Http;


/**
 * Class Response
 * @package Wave\Framework\Http
 *
 * @codeCoverageIgnore
 */
class Response
{
    protected $headers = array();
    protected $protocol = 'HTTP/1.1';
    private $status = null;
    protected $requestHeaders = array();

    const HTTP_V1_0 = 1.0;
    const HTTP_V1_1 = 1.1;


    public function __construct($protocol = 1.1, $requestHeaders = array())
    {
        $this->protocol = $protocol;
        $this->requestHeaders = $requestHeaders;
    }

    public function header($header)
    {
        array_push($this->headers, $header);

        return $this;
    }

    public function OK()
    {
        $this->header(sprintf('HTTP/%01.1f 200 OK', $this->protocol));
        $this->status = 200;
        return $this;
    }

    public function notFound()
    {
        $this->header(sprintf('HTTP/%01.1f 404 Not Found', $this->protocol));
        $this->status = 404;
        return $this;
    }

    public function forbidden()
    {
        $this->header(sprintf('HTTP/%01.1f 403 Forbidden', $this->protocol));
        $this->status = 403;
        return $this;
    }

    public function unauthorized()
    {
        $this->header(sprintf('HTTP/%01.1f 401 Unauthorized', $this->protocol));
        $this->status = 401;
        return $this;
    }

    public function badRequest()
    {
        $this->header(sprintf('HTTP/%01.1f 400 Bad Request', $this->protocol));
        $this->status = 400;
        return $this;
    }

    public function internalError()
    {
        $this->header(sprintf('HTTP/%01.1f 500 Internal Server Error', $this->protocol));
        $this->status = 500;
        return $this;
    }

    public function serviceUnavailable()
    {
        $this->header(sprintf('HTTP/%01.1f 503 Service Unavailable', $this->protocol));
        $this->status = 503;
        return $this;
    }

    /**
     * @param bool $enabled
     * @param int  $ttl Seconds to cache for, ignored, if $enabled is false
     */
    public function cache($enabled = true, $ttl = 360)
    {
        if ($enabled === true) {
            $timestamp = gmdate("D, d M Y H:i:s", time() + $ttl) . " GMT";
            $this->header(sprintf("Expires: %s", $timestamp), true);
            $this->header("Pragma: cache");
            $this->header(sprintf("Cache-Control: max-age=%s", $ttl), true);
        } else {
            $timestamp = gmdate("D, d M Y H:i:s") . " GMT";
            $this->header(sprintf("Expires: %s", $timestamp));
            $this->header(sprintf("Last-Modified: %s", $timestamp));
            $this->header("Pragma: no-cache");
            $this->header("Cache-Control: no-cache, must-revalidate");
        }
    }

    public function send()
    {
        if (!empty($this->headers)) {
            foreach ($this->headers as $header) {
                if (preg_match('/^HTTP\/1\.[0-1]\s[100-900]{3}/i', $header)) {
                    header($header, true, $this->status);
                    continue;
                }

                header($header, true);
            }
        }

        echo ob_get_clean();
    }
}
