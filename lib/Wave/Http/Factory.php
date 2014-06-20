<?php
namespace Wave\Http;

class Factory
{

    /**
     * A dynamic factory which instantiates the objects
     *
     * @param object $request
     *            The request handler
     * @param object $response
     *            The response handler
     * @throws \LogicException if $_SERVER variable is not presented
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
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
