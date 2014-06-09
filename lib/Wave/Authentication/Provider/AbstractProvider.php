<?php

namespace Wave\Authentication\Provider;

abstract class AbstractProvider
{
    /**
     * @param string $container The container name.
     */
    abstract public function setContainer($container);
    
    /**
     * Injects the adapter
     * 
     * @param \Wave\Authentication\Adapter\AbstractAdapter $adapter adapter instance
     */
    abstract public function setAdapter($adapter);
    
    /**
     * Returns the identity as container instance
     */
    abstract public function getIndentity();
    
    /**
     * Do the authentication
     */
    abstract public function authenticate($creadential, $secret);
    
    /**
     * Validates the authentication tokens with the $callback
     * 
     * @param string $credential the credential string
     * @param string $secret the secret string
     * @param callable $callback the callback for the verification
     */
    abstract public function validate($credential, $secret, $callback);
}
