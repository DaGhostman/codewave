<?php
namespace Wave\Framework\Http\Entities\Parameters;

use Wave\Framework\Exceptions\InvalidKeyException;
use Wave\Framework\Interfaces\Http\RequestInterface;
use Wave\Framework\Interfaces\Http\ParametersInterface;

/**
 * Class JsonObject
 *
 * Helper object to allow seamless usage of JSON data send along with the requests
 *
 * @package Wave\Framework\Http\Entities\Parameters
 */
class Json implements ParametersInterface, \ArrayAccess
{
    private $parameters = [];

    public function __construct(RequestInterface $request)
    {
        if (strlen(trim($request->getBody())) > 0) {
            $this->parameters = json_decode($request->getBody(), true);
        }
    }

    public function __toString()
    {
        return json_encode($this->parameters);
    }

    /**
     * Retrieve entry based on $name
     *
     * @param $name string
     * @throws \Wave\Framework\Exceptions\InvalidKeyException
     *
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
     * Checks if $name exists in the list of request parameters
     * @param $name string
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * Adds/Updates an entry in the list of parameters.
     *
     * Primary intention of this method is when passing data to instances
     * of ResponseInterface as Request Parameters should be always immutable
     *
     * All value types are valid, except resource
     *
     * @see http://php.net/manual/en/function.json-encode.php#refsect1-function.json-encode-parameters
     *
     * @param $name string
     * @param $value mixed
     *
     * @return $this
     */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Remove an entry from the list of parameters, if it does not exist
     * an exception is thrown
     *
     * @param $name string
     * @throws \Wave\Framework\Exceptions\InvalidKeyException
     * @return null
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

    public function export()
    {
        return $this->parameters;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
