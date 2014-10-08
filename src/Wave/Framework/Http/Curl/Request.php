<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 24/08/14
 * Time: 00:50
 */

namespace Wave\Framework\Http\Curl;

/**
 * Class Request
 * @package Wave\Framework\Http\Curl
 *
 * @codeCoverageIgnore
 */
class Request
{
    protected $curl = null;

    protected $headers = array();
    private $verifySSL = false;
    private $timeout = 3;
    private $withHeaders = false;

    public function __construct()
    {

        if (!extension_loaded('curl')) {
            throw new \RuntimeException(
                sprintf("Curl extension not loaded")
            );
        }

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_VERBOSE, true);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    }

    public function withAuthorization($string)
    {
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, true);
        curl_setopt($this->curl, CURLOPT_USERPWD, $string);

        return $this;
    }

    public function setUrl($url)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);

        return $this;
    }


    public function setPort($port)
    {
        curl_setopt($this->curl, CURLOPT_PORT, $port);

        return $this;
    }

    public function withHeaders($bool = true)
    {
        $this->withHeaders = $bool;

        return $this;
    }

    /**
     *
     *
     * @param $data array key => value pairs of data to send
     *
     * @return $this
     */
    public function withPostData($data)
    {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);

        return $this;
    }

    public function setMethod($method = 'GET')
    {
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        return $this;
    }
    public function setUA($userAgent)
    {
        curl_setopt($this->curl, CURLOPT_USERAGENT, $userAgent);

        return $this;
    }

    public function setHeader($header)
    {
        array_push($this->headers, $header);

        return $this;
    }

    public function setEncoding($encoding)
    {
        $this->setHeader(sprintf("Accept-Encoding: %s", $encoding));

        return $this;
    }

    public function setCharset($charset)
    {
        $this->setHeader(sprintf("Accept-Charset: %s", $charset));

        return $this;
    }

    public function setTimeout($seconds)
    {
        $this->timeout = (int) $seconds;

        return $this;
    }

    public function verifySSL($bool)
    {
        $this->verifySSL = $bool;

        return $this;
    }

    public function send()
    {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $this->verifySSL);

        if (!empty($this->headers)) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        }
        curl_setopt($this->curl, CURLOPT_HEADER, $this->withHeaders);
        curl_setopt($this->curl, CURLOPT_ENCODING, '');
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);

        curl_setopt($this->curl, CURLOPT_FORBID_REUSE, true);

        if (!($data = curl_exec($this->curl))) {

            print curl_error($this->curl);
            print curl_errno($this->curl);
            return null;
        }

        return new Response($this->curl, $data);
    }
}
