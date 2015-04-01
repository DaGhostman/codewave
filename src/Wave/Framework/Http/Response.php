<?php
namespace Wave\Framework\Http;

use Psr\Http\Message\ResponseInterface;
use Wave\Framework\Abstracts\AbstractLinkable;
use Wave\Framework\Common\Link;

class Response extends AbstractLinkable
{

    public function __construct($response)
    {
        if (!$response instanceof ResponseInterface) {
            throw new \InvalidArgumentException(
                sprintf('Expected ResponseInterface, received \'%s\'', get_class($response))
            );
        }

        parent::__construct($response);
    }

    public function __call($name, array $args = [])
    {
        if (method_exists($this->instance, $name)) {
            $result = call_user_func_array([$this->instance, $name], $args);
            if (substr($name, 0, 3) !== 'get') {
                $this->notify();
                $this->instance = $result;
                return $this;
            }

            return $result;
        }

        return $this;
    }
}
