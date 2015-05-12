<?php
namespace Wave\Framework\Interfaces\Http;

interface ServerInterface
{
    /**
     * Accepts a request object and switches it's Stream to 'php://input',
     * which allows to retrieve the body of the request (usually in POST requests).
     * And will construct a ResponseInterface object as linkable which is then picked
     * up by the application class and linked.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $source
     *
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, array $source = null);

    /**
     * Starts output buffering with `ob_gzhandler` and
     * invokes the callback provided in Wave::run
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function listen(callable $callback = null);

    public function getRequest();

    public function getResponse();
}