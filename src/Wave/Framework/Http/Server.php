<?php
namespace Wave\Framework\Http;

use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;

class Server
{
    /**
     * @type Request
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
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param $source array
     *
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, array $source = null)
    {
        if ($source === null) {
            $source = filter_input_array(INPUT_SERVER);
        }
        $this->bufferLevel = ob_get_level();


        $this->request = $request->addHeaders($this->buildHeaders($source), false);
        $this->response = $response;
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

        $result = call_user_func($callback, $this->request, $this->response);

        if (is_array($result) || (is_object($result) && $result instanceof ResponseInterface) || is_callable($result)) {
            throw new \RuntimeException(
                sprintf(
                    'Unexpected result type "%s" from callback,' .
                    ' valid return types are all except: callable, array, object',
                    gettype($result)
                )
            );
        }

        echo $result;

        $this->send();
    }

    /**
     * Sends the headers, outputs the output buffer contents and the contents of the response object
     */
    private function send()
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
        $status = $response->getStatus();

        header(sprintf(
            'HTTP/%s %d %s',
            $response->getVersion(),
            $status[0],
            $status[1]
        ), true, $status[0]);

        foreach ($response->getHeaders() as $name => $values) {
            //$name  = $this->filterHeader($header);
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

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    private function buildHeaders($server)
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (strpos($key, 'HTTP_COOKIE') === 0) {
                // Cookies are handled using the $_COOKIE super global
                continue;
            }
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
                $name = strtolower($name);
                $headers[$name] = $value;
                continue;
            }
            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = substr($key, 8); // Content-
                $name = 'Content-' . (($name === 'MD5') ? $name : ucfirst(strtolower($name)));
                $name = strtolower($name);
                $headers[$name] = $value;
                continue;
            }
        }
        return $headers;
    }
}
