<?php
namespace Wave\Framework\Http\Entities\Parameters;

use Wave\Framework\Exceptions\InvalidKeyException;
use Wave\Framework\Interfaces\Http\ParametersInterface;
use Wave\Framework\Interfaces\Http\RequestInterface;

/**
 * Class Regular
 * @package Wave\Framework\Http\Entities\Parameters
 */
class Regular implements ParametersInterface, \ArrayAccess
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @param RequestInterface $request
     */
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
        return http_build_query($this->parameters);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return http_build_query($this->parameters);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
