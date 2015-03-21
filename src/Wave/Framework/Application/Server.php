<?php
namespace Wave\Framework\Application;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Wave\Framework\Http\Server\Response;

/**
 * Class Server
 *
 * Class responsible to glue the Request and Response to
 * the route dispatching. It takes care to build the params
 *
 * @package Wave\Framework\Application
 */
class Server
{
    /**
     * @var array
     */
    protected $server;

    /**
     * @var RequestInterface
     */
    protected $request  = null;

    /**
     * @var ResponseInterface
     */
    protected $response = null;

    /**
     * @var \ArrayObject
     */
    protected $params = null;

    /**
     * Constructs a for the object.
     * Responsible to define the Request and Response
     * for the request, as well as to allow mocking
     * of the environment using the 3rd parameter,
     * which responds to $_SERVER, or alternatively
     * an array with the mocking values
     *
     * @param $request RequestInterface
     * @param $response ResponseInterface
     * @param $variables array
     */
    public function __construct($request, $response, $variables = [])
    {
        if (!$request instanceof RequestInterface) {
            throw new \InvalidArgumentException(
                'Invalid request object'
            );
        }

        // Might not be as per PSR-7 @TODO
        foreach ($variables as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $request->withHeader($header, $value);
        }

        $this->request = $request;

        if (!$response instanceof Response) {
            throw new \InvalidArgumentException(
                'Invalid response object'
            );
        }

        $this->response = $response;

    }

    /**
     * Invokes the wraps the dispatcher,
     * if the result is a string adds it as output to the
     * response object as per PSR-5 or if it is a instance
     * of Response replaces the current response object.
     *
     * @param callable $callback Callback wrapping the
     * dispatcher.
     * @return ResponseInterface A response object populated with
     * the output of the call
     */
    public function dispatch(callable $callback)
    {
        $result = call_user_func($callback, $this->request, $this->response);

        if (!is_string($result) && !is_null($result) && !$result instanceof Response) {
            throw new \RuntimeException(
                'Expected string or instance of Response as function return type.'
            );
        }

        if (is_string($result)) {
            $this->response->getBody()->write($result);
        } elseif ($result instanceof Response) {
            $this->response = $result;
        }

        return $this->response;
    }

    /**
     * Handles the sending of the response headers
     *
     * @param ResponseInterface $response
     *
     * @codeCoverageIgnore
     */
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

    /**
     * Simple filtering of the headers, used by
     * Server::sendHeaders()
     *
     * @param $header
     * @return mixed
     *
     * @codeCoverageIgnore
     */
    private function filterHeader($header)
    {
        $filtered = str_replace('-', ' ', $header);
        $filtered = ucwords($filtered);
        return str_replace(' ', '-', $filtered);
    }

    /**
     * Sends the response to the client.
     * If there are some headers already sent, skips the
     * sending of the headers and sends the output.
     *
     *
     * @return null This should always be the last step of the request
     * and therefore should not need to return anything.
     */
    public function send()
    {
        if (!headers_sent()) {
            $this->sendHeaders($this->response);
        }

        if ($this->response instanceof Response) {
            echo $this->response->getBody();
        }

        return null;
    }
}
