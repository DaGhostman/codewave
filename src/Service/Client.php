<?php
namespace Wave\Framework\Service;

use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;

class Client
{
    protected $request;
    private $options = [
        'withHeaders' => false,
        'followRedirect' => true,
    ];

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->curl = curl_init();
    }

    public function getCurl()
    {
        return $this->curl;
    }

    public function withHeaders()
    {

        $this->options['withHeaders'] = true;
        return $this;
    }

    public function followRedirects($switch)
    {
        $this->options['followRedirect'] = $switch;

        return true;
    }

    private function parseHeaders($headerString)
    {
        $parsed = [];
        $headers = explode(PHP_EOL, trim($headerString));

        /**
         * The status code
         */
        $status = 200;

        foreach ($headers as $line) {
            if (substr($line, 0, 4) === 'HTTP') {
                preg_match('#(\d{3})#', $line, $matches);

                $status = (int) $matches[0];
                continue;
            }

            list($header, $value)=[substr($line, 0, strpos($line, ':')), trim(substr($line, strpos($line, ':')+1))];
            if (strlen($header) > 0) {
                $parsed[$header] =$value;
            }
        }

        return [$status, $parsed];
    }

    public function send(ResponseInterface $response, $dataCallback = null)
    {
        $ch = $this->curl;

        $headers = [];
        foreach ($this->request->getHeaders() as $header => $value) {
            $headers[] = $header . ': ' . $value[0];
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->request->getMethod());
        curl_setopt($ch, CURLOPT_URL, $this->request->getUrl());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, $this->options['withHeaders']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->options['followRedirect']);

        $data = $this->request->getBody();
        if ($data !== null) {
            if ($dataCallback !== null) {
                $data = call_user_func($dataCallback, $this->request->getBody());
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $result = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        if ($this->options['withHeaders']) {
            list($status, $headers) = $this->parseHeaders(substr($result, 0, $headerSize));

            $response->setStatus($status);
            $response->addHeaders($headers, false);
        }

        $response->setBody(substr($result, ($this->options['withHeaders'] ? $headerSize : 0)));

        return $response;
    }
}
