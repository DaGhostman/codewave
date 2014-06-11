<?php
namespace Wave\Authentication\Provider;

class BasicProvider extends AbstractProvider
{

    protected $identity;

    protected $adapter;

    protected $container;

    /**
     * (non-PHPdoc)
     * 
     * @see \Wave\Authentication\Provider\AbstractProvider::setAdapter()
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Wave\Authentication\Provider\AbstractProvider::setContainer()
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Wave\Authentication\Provider\AbstractProvider::validate()
     */
    public function validate($credential, $secret, $callback)
    {
        if ($callback instanceof \Closure) {
            return $callback($credential, $secret);
        }
        if (is_callable($callback)) {
            return call_user_func_array($callback, array(
                $credential,
                $secret
            ));
        }
        
        return null;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \Wave\Authentication\Provider\AbstractProvider::authenticate()
     */
    public function authenticate($credential, $secret)
    {
        if (false !== ($identity = $this->adapter->get($credential . '[:]' . $secret)) &&
            null !== $this->adapter->get($credential . '[:]' . $secret)) {
            
            $this->identity = $this->container->populate($identity);
            
            return true;
        }
        
        return false;
    }

    public function getIndentity()
    {
        return $this->identity;
    }
}
