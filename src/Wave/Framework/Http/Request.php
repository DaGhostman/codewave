<?php
namespace Wave\Framework\Http;

use \Wave\Framework\Abstracts\AbstractLinkable;
use \Wave\Framework\Adapters\Link\Destination;

use Psr\Http\Message\RequestInterface;

class Request extends AbstractLinkable implements Destination
{
    /**
     * @param $request RequestInterface
     */
    public function __construct($request)
    {
        if (!$request instanceof RequestInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Expected instance of RequestInterface, %s received',
                get_class($request)
            ));
        }

        parent::__construct($request);
    }


    public function __call($name, array $args = [])
    {
        if (method_exists($this->instance, $name)) {
            $result = call_user_func_array([$this->instance, $name], $args);

            $class = get_class($this->instance);
            if ($result instanceof $class) {
                $this->instance = $result;
                $this->notify();

                return $this;
            }

            return $result;
        }

        throw new \RuntimeException(sprintf(
            'Trying to call non-existing method "%s" on class',
            $name,
            get_class($this->instance)
        ));
    }
}
