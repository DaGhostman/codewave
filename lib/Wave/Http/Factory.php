<?php
namespace Wave\Http;

class Factory
{

    /**
     * A dynamic factory whcih instantiates the objects
     *
     * @param string $request
     *            The request handler
     * @param string $response
     *            The reponse handler
     * @throws \LogicException if $_SERVER variable is not presented
     */
    public function __construct($request, $response, $environment)
    {
        $this->request = new $request($environment);
        $this->response = new $response();
    }

    /**
     * Returns a reference to the current request instance
     */
    public function &request()
    {
        return $this->request;
    }

    /**
     * Returns a reference to the current response instance
     */
    public function &response()
    {
        return $this->response;
    }
}
