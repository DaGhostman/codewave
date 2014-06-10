<?php

namespace Wave\Http;

class Factory
{
    /**
     * A dynamic factory whcih instantiates the objects
     * 
     * @param string $request The request handler
     * @param string $response The reponse handler
     * @throws \LogicException if $_SERVER variable is not presented
     */
    public function __construct($request, $response, $environment)
    {
        $this->request = new $request($environment);
        $this->response = new $response;
    }
    
    public function &request()
    {
        return $this->request;
    }
    
    public function &response()
    {
        return $this->response;
    }
}
