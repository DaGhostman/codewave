<?php
/**
 * Created by PhpStorm.
 * User: elham_asmar
 * Date: 30/03/2015
 * Time: 15:09
 */

namespace Wave\Framework\Http;


use Psr\Http\Message\ResponseInterface;
use Wave\Framework\Adapters\Link\Linkable;
use Wave\Framework\Common\Link;

class Response implements Linkable
{
    private $response;

    private $links = [];

    public function __construct($response)
    {
        if (!$response instanceof ResponseInterface) {
            throw new \InvalidArgumentException(
                sprintf('Expected ResponseInterface, received \'%s\'', get_class($response))
            );
        }

        $this->response = $response;
    }

    public function addLink(Link $link)
    {
        $this->links[] = $link;
    }

    public function update()
    {
        foreach ($this->links as $link) {
            $link->update($this);
        }

        return $this;
    }

    public function notify()
    {
        foreach ($this->links as $link) {
            $link->notify();
        }
    }

    public function __call($name, array $args = [])
    {
        var_dump($name);
        $this->response = call_user_func_array([$this->response, $name], $args);

        $this->update($this)
            ->notify();

        return $this->response;
    }

    public function getState()
    {
        return $this->response;
    }


    public function __set($name, $value)
    {
        $this->response->$name = $value;

        $this->update()
            ->notify();
    }
}