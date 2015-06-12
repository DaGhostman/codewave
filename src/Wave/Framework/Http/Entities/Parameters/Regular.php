<?php
namespace Wave\Framework\Http\Entities\Parameters;

use Wave\Framework\Exceptions\InvalidKeyException;
use Wave\Framework\Interfaces\Http\ParametersInterface;
use Wave\Framework\Interfaces\Http\RequestInterface;

class Regular implements ParametersInterface
{
    private $parameters = [];

    public function __construct(RequestInterface $request)
    {
        if (strlen(trim($request->getBody())) > 0) {
            parse_str($request->getBody(), $this->parameters);
        }
    }

    /**
     * Return a parameter with $name
     *
     * @param string $name
     * @throws InvalidKeyException
     * @return mixed
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new InvalidKeyException(sprintf(
                'Key %s does not exist in list of parameters',
                $name
            ));
        }

        return $this->parameters[$name];
    }

    /**
     * Add a new parameter
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Check if a parameter exists
     *
     * @param string $name
     *
     * @return mixed
     */
    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * Remove a parameter
     * @throws InvalidKeyException
     * @param string $name
     */
    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new InvalidKeyException(sprintf(
                'Key %s does not exist in list of parameters',
                $name
            ));
        }

        unset($this->parameters[$name]);
    }

    /**
     * Returns an array with all the parameters
     *
     * @return array
     */
    public function export()
    {
        return $this->parameters;
    }

    public function __toString()
    {
        return http_build_query($this->parameters);
    }
}