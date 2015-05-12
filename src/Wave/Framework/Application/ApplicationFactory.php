<?php
namespace Wave\Framework\Application;

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
     * @param string $class
     * @param array $headers
     * @param string $body
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
     * @param string $class
     *
     * @return $this
     */
    public function setResponseClass($class)
    {
        $this->checkClass($class);
        $this->responseClass = $class;

        return $this;
    }

    public function setServerClass($class)
    {
        $this->checkClass($class);
        $this->serverClass = $class;

        return $this;
    }

    /**
     * @param UrlInterface $url
     * @param callable $callback
     *
     * @return object
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

        $responseReflection = new \ReflectionClass($this->responseClass);
        $response = $responseReflection->newInstance();

        $serverReflection = new \ReflectionClass($this->serverClass);
        return $serverReflection->newInstance($request, $response, $this->serverVariables);
    }

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
