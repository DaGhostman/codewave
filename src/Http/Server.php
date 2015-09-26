<?php
namespace Wave\Framework\Http;

use Wave\Framework\Application\Router;
use Wave\Framework\Exceptions\HttpNotAllowedException;
use Wave\Framework\Exceptions\HttpNotFoundException;
use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;
use Wave\Framework\Interfaces\Http\ServerInterface;
use Wave\Framework\Interfaces\Middleware\MiddlewareAwareInterface;
use Wave\Framework\Interfaces\Middleware\MiddlewareInterface;

/**
 * Class Server
 * @package Wave\Framework\Http
 */
class Server implements ServerInterface, MiddlewareAwareInterface
{
    /**
     * @type RequestInterface
     */
    protected $request;

    /**
     * @type ResponseInterface
     */
    protected $response;

    /**
     * @type callable
     */
    protected $callback;

    /**
     * @type int
     */
    protected $bufferLevel;

    /**
     * @var array
     */
    private $middleware = [];

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
            $source = filter_input_array(INPUT_SERVER, FILTER_DEFAULT);
        }
        $this->bufferLevel = ob_get_level();


        $this->request = $request->addHeaders($this->buildHeaders($source), false);
        $this->response = $response;
    }

    /**
     * Responsible for invoking the $callback as well as trigger the
     * middleware stack. Note that the middleware is running as if
     * going through layers of onion, the layer you first enter is
     * the one which you pass before you exit.
     *
     *
     * @param Router $router
     * @throws \InvalidArgumentException
     * @throws HttpNotAllowedException
     * @throws HttpNotFoundException
     * @throws \Exception
     * @return $this
     */
    public function listen($router)
    {

        if (!$router instanceof Router) {
            throw new \InvalidArgumentException(
                'Passed argument must be instance of Application\Router'
            );
        }

        if ($this->request->getMethod() === 'TRACE') {
            foreach ($this->request->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    echo sprintf(
                        '%s: %s' . "\n\r",
                        $name,
                        $value
                    );
                }
            }
        }

        if ($this->request->getMethod() !== 'TRACE') {
            // Invoke the middleware stack, as FIFO
            /**
             * @var $middleware MiddlewareInterface
             */
            foreach ($this->middleware as $middleware) {
                $middleware->before($this->request, $this->response);
            }

            try {
                $router->dispatch(
                    $this->request
                        ->getMethod(),
                    $this->request
                        ->getUrl()
                        ->getPath()
                );
            } catch (HttpNotFoundException $e) {
                $this->response->setStatus(404);
                throw $e;
            } catch (HttpNotAllowedException $e) {
                $this->response->setStatus(405);
                $this->response->addHeader('Allow', implode(', ', $e->getAllowed()));
                throw $e;
            } catch (\Exception $e) {
                list($statusCode)=$this->response->getStatus();
                if ($statusCode >= 200 && $statusCode <= 208) {
                    $this->response->setStatus(500);
                }
                throw $e;
            }

            // Invoke the middleware stack as LIFO
            foreach (array_reverse($this->middleware) as $middleware) {
                $middleware->after($this->response);
            }
        }

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

        echo $this->response->getBody();
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
            'HTTP/%0.1f %d %s',
            $response->getVersion(),
            $status[0],
            $status[1]
        ), true, $status[0]);

        foreach ($response->getHeaders() as $name => $values) {
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
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Adds middleware instances to the middleware stack
     *
     * @param \Wave\Framework\Interfaces\Middleware\MiddlewareInterface $middleware
     *
     * @return null
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @param $server
     * @return array
     */
    private function buildHeaders($server)
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (strpos($key, 'HTTP_COOKIE') === 0) {
                // Cookies are handled using the $_COOKIE super global
                continue;
            }
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = str_replace('_', ' ', substr($key, 5));
                $name = str_replace(' ', '-', ucwords(strtolower($name)));
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