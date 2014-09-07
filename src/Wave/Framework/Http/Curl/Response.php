<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 24/08/14
 * Time: 00:50
 */

namespace Wave\Framework\Http\Curl;


/**
 * Class Response
 * @package Wave\Framework\Http\Curl
 *
 * @codeCoverageIgnore
 */
class Response
{
    protected $curl = null;

    public function __construct($curl, $data = null)
    {
        $this->curl = $curl;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function status()
    {
        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    }

    public function time()
    {
        return curl_getinfo($this->curl, CURLINFO_TOTAL_TIME);
    }

    public function length()
    {
        return curl_getinfo($this->curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    }

    public function contentType()
    {
        return curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getHeaderSize()
    {
        return curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }
}
