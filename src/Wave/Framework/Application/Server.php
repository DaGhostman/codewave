<?php
namespace Wave\Framework\Application;

use Wave\Framework\Http\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Wave\Framework\Http\Server\Response;

class Server
{
    protected $server;
    protected $uri;
    protected $request  = null;
    protected $response = null;

    public function __construct($request, $response, $server = null)
    {
        if (!$request instanceof RequestInterface) {
            throw new \InvalidArgumentException(
                'Invalid request object specified'
            );
        }

        $this->response = $response;

        $this->request = $request;

        $this->server = $server;

        if (is_null($server)) {
            $this->server = filter_input_array(INPUT_SERVER, FILTER_FLAG_NONE);
        }
    }

    public function dispatch(callable $callback)
    {
        $result = call_user_func($callback, $this->request, $this->response);

        if (!is_string($result) && !$result instanceof Response) {
            throw new \RuntimeException(
                'Expected string or intanse of Response as function return type.'
            );
        }

        if (is_string($result)) {
            $this->response->getBody()->write($result);
        } elseif ($result instanceof Response) {
            $this->response = $result;
        }

        return $this->response;
    }

    private function sendHeaders(ResponseInterface $response)
    {
        if ($response->getReasonPhrase()) {
            header(sprintf(
                'HTTP/%s %d %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        } elseif (!$response->getReasonPhrase()) {
            header(sprintf(
                'HTTP/%s %d',
                $response->getProtocolVersion(),
                $response->getStatusCode()
            ));
        }
        foreach ($response->getHeaders() as $header => $values) {
            $name  = $this->filterHeader($header);
            $first = true;
            foreach ($values as $value) {
                header(sprintf(
                    '%s: %s',
                    $name,
                    $value
                ), $first);
                $first = false;
            }
        }
    }

    private function filterHeader($header)
    {
        $filtered = str_replace('-', ' ', $header);
        $filtered = ucwords($filtered);
        return str_replace(' ', '-', $filtered);
    }

    public function send()
    {
        if (!headers_sent()) {
            $this->sendHeaders($this->response);
        }

        echo $this->response->getBody();
    }
}
