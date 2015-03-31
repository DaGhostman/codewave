<?php
/**
 * Created by PhpStorm.
 * User: elham_asmar
 * Date: 30/03/2015
 * Time: 12:39
 */
namespace Wave\Framework\Http;

use \Wave\Framework\Adapters\Link\Linkable;
use \Wave\Framework\Adapters\Link\Destination;
use \Wave\Framework\Common\Link;

use Psr\Http\Message\RequestInterface;

class Request implements Linkable, Destination
{
    /**
     * @var $link array[\Wave\Framework\Common\Link]
     */
    private $links = [];

    /**
     * @var $request RequestInterface
     */
    protected $request = null;

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

        $this->request = $request;
    }

    public function notify()
    {
        foreach ($this->links as $link) {
            $link->update($this);
        }

        return $this;
    }

    public function addLink(Link $link)
    {
        $this->links[] = $link;

        return $this;
    }

    public function getState()
    {
        return $this->request;
    }

    public function setPsrRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    public function __call($name, array $args = [])
    {
        $this->request = call_user_func_array([$this->request, $name], $args);

        $this->update($this)
            ->notify();

        return $this->request;
    }

    public function __set($name, $value)
    {
        $this->request->$name = $value;

        $this->update()
            ->notify();
    }

    public function update()
    {
        foreach ($this->links as $link) {
            $link->update($this);
        }

        return $this;
    }
}
