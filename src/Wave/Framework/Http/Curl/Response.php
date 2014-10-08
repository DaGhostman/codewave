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
class Response implements \ArrayAccess, \IteratorAggregate
{
    protected $curl = null;

    protected $headers = array();

    protected $body = null;

    public function __construct($curl, $data = null)
    {
        $this->curl = $curl;

        $headerString = trim(substr($data, 0, $this->getHeaderSize()));
        foreach (explode("\n\r", $headerString) as $pair) {
            list($header, $value)=explode(':', $pair);
            $this->headers[$header] = $value;
        }


        $this->data = trim(substr($data, $this->getHeaderSize()));
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

    public function getJsonAsArray()
    {
        return json_decode($this->data, true);
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

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->headers);
    }

    public function offsetGet($key)
    {
        if (!array_key_exists($key, $this->headers)) {
            return null;
        }

        return $this->headers[$key];
    }

    public function offsetSet($ket, $value)
    {
        throw new \LogicException('Unable to write. Curl\Response object is read-only.');
    }

    public function offsetUnset($key)
    {
        throw new \LogicException('Unable to write. Curl\Response object is read-only.');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->headers, \ArrayIterator::ARRAY_AS_PROPS);
    }
}
