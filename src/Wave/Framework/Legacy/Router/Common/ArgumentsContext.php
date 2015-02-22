<?php
namespace Wave\Framework\Legacy\Router\Common;

use Traversable;

/**
 * Class ArgumentsContext
 *
 * @package Wave\Framework\Application\Contexts
 *
 * @deprecated This backward compatibility set will
 *             be removed in version 3.5. Please
 *             ensure that you migrate to the new
 *             routes generation methods.
 */
class ArgumentsContext implements \IteratorAggregate
{

    /**
     *
     * @var object Object representing current scope
     */
    protected $scope = null;

    /**
     *
     * @var array Entity storage
     */
    protected $store = [];

    public function __construct($scope, array $data = array())
    {
        $this->scope = $scope;
        $this->store = $data;
    }

    /**
     *
     * @param $val mixed
     *            Resolves the type of the variable if string,
     *            returns the argument otherwise
     *
     * @return array|bool|float|int
     */
    public function resolveType($val)
    {
        if (strtolower($val) == 'true') {
            return true;
        } elseif (strtolower($val) == 'false') {
            return false;
        } elseif (is_numeric($val)) {
            // Numeric value, determine if int or float and then cast
            if ((float) $val == (int) $val) {
                return (int) $val;
            }

            return (float) $val;
        }

        return (count(explode('/', $val)) > 1) ? explode('/', $val) : $val;
    }

    public function fetch($key)
    {
        if (! array_key_exists($key, $this->store)) {
            return null;
        }
        return $this->resolveType($this->store[$key]);
    }

    public function scope()
    {
        return $this->scope;
    }

    /**
     * Adds an entity to the context, identified by a key
     *
     * @param $key string
     *            Key of the entity
     * @param $value mixed
     *            Value of the entity
     *
     * @return $this
     */
    public function push($key, $value)
    {
        if (! array_key_exists($key, $this->store)) {
            $this->store[$key] = $value;
        }
    }

    /**
     *
     * @param $key string
     *            Alias for fetch
     *
     * @return array|bool|float|int|null
     */
    public function get($key)
    {
        return $this->fetch($key);
    }

    public function __get($key)
    {
        return $this->fetch($key);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *         <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->store);
    }
}
