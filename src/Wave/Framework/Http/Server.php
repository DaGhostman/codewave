<?php
namespace Wave\Framework\Http;

use Phly\Http\Response;
use Phly\Http\ServerRequest;
use Phly\Http\Stream;
use Psr\Http\Message\RequestInterface;
use Wave\Framework\Http\Response as LinkResponse;
use Wave\Framework\Adapters\Link\Destination;

class Server implements Destination
{
    /**
     * @type ServerRequest
     */
    protected $request;

    /**
     * @type Response
     */
    protected $response;

    /**
     * @type callable
     */
    protected $callback;

    /**
     * @param $request Request
     */

    protected $bufferLevel;

    /**
     * Accepts a request object and switches it's Stream to 'php://input',
     * which allows to retrieve the body of the request (usually in POST requests).
     * And will construct a ResponseInterface object as linkable which is then picked
     * up by the application class and linked.
     *
     * @param $request RequestInterface
     */
    public function __construct($request)
    {
        if (!$request instanceof RequestInterface && !$request instanceof Request) {
            throw new \InvalidArgumentException(
                sprintf('Class "%s" does not implement RequestInterface', get_class($request))
            );
        }
        $this->request = $request->withBody(new Stream('php://input'), 'r+b');
        $this->response = new LinkResponse(new Response());
    }

    /**
     * Starts output buffering with `ob_gzhandler` and
     * invokes the callback provided in Wave::run
     *
     * @param callable $callback
     * @return $this
     */
    public function listen(callable $callback = null)
    {
        if (!headers_sent()) {
            ob_start('ob_gzhandler');
        }
        $this->bufferLevel = ob_get_level();
        $result = call_user_func($callback, $this->request, $this->response);

        if (is_array($result) || is_object($result) || is_callable($result)) {
            throw new \RuntimeException(
                sprintf(
                    'Unexpected result type "%s" from callback,' .
                    ' valid return types are all except: callable, array, object',
                    gettype($result)
                )
            );
        }

        $this->response->getBody()->write($result);

        return $this;
    }

    /**
     * Sends the headers, outputs the output buffer contents and the contents of the response object
     */
    public function send()
    {
        if (!headers_sent()) {
            $this->sendHeaders();
        }

        if (ob_get_length() > 0) {
            while (ob_get_level() >= $this->bufferLevel) {
                ob_get_flush();
            }
        }

        $this->bufferLevel = null;

        echo $this->response
            ->getBody();
    }

    /**
     * Send response headers
     *
     * Sends the response status/reason, followed by all headers;
     * header names are filtered to be word-cased.
     */
    private function sendHeaders()
    {
        $response = $this->response;
        if ($response->getReasonPhrase()) {
            header(sprintf(
                'HTTP/%s %d %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        } else {
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
     * Filter a header name to wordcase
     *
     * @param string $header
     * @return string
     */
    private function filterHeader($header)
    {
        $filtered = str_replace('-', ' ', $header);
        $filtered = ucwords($filtered);
        return str_replace(' ', '-', $filtered);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \InvalidArgumentException(
            sprintf('Value "%s" not found in class', $name)
        );
    }
}
