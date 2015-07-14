<?php
namespace Wave\Framework\Interfaces\Http;

use Wave\Framework\Application\Router;

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
     * Invokes the callback provided in ServerInterface::run
     *
     * @param mixed $router
     *
     * @return $this
     */
    public function listen($router);

    public function getRequest();

    public function getResponse();
}
