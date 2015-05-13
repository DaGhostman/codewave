<?php
namespace Wave\Framework\Application;

use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ResponseInterface;
use Wave\Framework\Interfaces\Http\UrlInterface;

class ApplicationFactory
{
    private $requestClass = '\Wave\Framework\Http\Request';
    private $requestHeaders = [];
    private $requestBodySource;
    private $responseClass = '\Wave\Framework\Http\Response';
    private $serverClass = '\Wave\Framework\Http\Server';

    private $serverVariables = [];

    public function __construct(array $server)
    {
        $this->serverVariables = $server;
    }

    /**
     * Defines the class which is going to be injected when constructing the Server object.
     * All other fields are to allow passing arguments to the object's constructor (as it
     * otherwise return new instance and this will make the construction process more complicated.)
     *
     * Note that the class name that is passed to this method needs to implement the RequestInterface,
     * otherwise when constructing the server object (the point at which all classes get instantiated)
     * an RuntimeException is thrown.
     *
     * @param string $class Name of the class
     * @param array $headers array of pre-configured headers inject to the object
     * @param string $body The stream to use when retrieving the request's body, defaults to 'php://input'
     *
     * @return $this
     */
    public function setRequest($class, array $headers = [], $body = 'php://input')
    {
        $this->checkClass($class);
        $this->requestClass = $class;
        $this->requestHeaders = $headers;
        $this->requestBodySource = $body;

        return $this;
    }

    /**
     * Defines the class which is going to be injected when constructing the Server object.
     *
     * Note that the class name that is passed to this method needs to implement the ResponseInterface,
     * otherwise when constructing the server object (the point at which all classes get instantiated)
     * an RuntimeException is thrown.
     *
     * @param string $class The class name
     *
     * @return $this
     */
    public function setResponseClass($class)
    {
        $this->checkClass($class);
        $this->responseClass = $class;

        return $this;
    }

    /**
     * Defines the class which is going to be used as Server object.
     *
     * Note that the class name that is passed to this method needs to implement the ServerInterface,
     * otherwise when constructing the server object (the point at which all classes get instantiated)
     * an RuntimeException is thrown.
     * @param $class
     *
     * @return $this
     */
    public function setServerClass($class)
    {
        $this->checkClass($class);
        $this->serverClass = $class;

        return $this;
    }

    /**
     * Dynamically instantiates all classes and constructs
     * a Server object with its dependencies as per
     * \Wave\Framework\Interfaces\Http\ServerInterface
     *
     * The server object is the one responsible to instantiate the application
     * logic and enter user-land code space.
     *
     * @param UrlInterface $url
     *
     * @return object
     * @throws \RuntimeException if any of the classes do not implement their respective interface
     */
    public function build(UrlInterface $url)
    {
        $requestReflection = new \ReflectionClass($this->requestClass);
        $request = $requestReflection->newInstance(
            $this->serverVariables['REQUEST_METHOD'],
            $url,
            $this->requestHeaders,
            $this->requestBodySource
        );

        if (!$request instanceof RequestInterface) {
            throw new \RuntimeException(sprintf(
                'The request class needs to implement \Wave\Framework\Interfaces\Http\RequestInterface'
            ));
        }

        $responseReflection = new \ReflectionClass($this->responseClass);
        $response = $responseReflection->newInstance();

        if (!$response instanceof ResponseInterface) {
            throw new \RuntimeException(sprintf(
                'The response class needs to implement \Wave\Framework\Interfaces\Http\ResponseInterface'
            ));
        }

        $serverReflection = new \ReflectionClass($this->serverClass);
        $server = $serverReflection->newInstance($request, $response, $this->serverVariables);

        if (!$server instanceof RequestInterface) {
            throw new \RuntimeException(sprintf(
                'The request class needs to implement \Wave\Framework\Interfaces\Http\ServerInterface'
            ));
        }

        return $server;
    }

    /**
     * A minimal method to avoid repeating the check and if the class exists
     * and throw exception
     *
     * @param $class string
     */
    private function checkClass($class)
    {
        if (!class_exists($class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Provided class "%s" does not exist',
                $class
            ));
        }
    }
}
