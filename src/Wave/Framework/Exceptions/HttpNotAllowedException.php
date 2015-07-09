<?php
namespace Wave\Framework\Exceptions;

/**
 * Class HttpNotAllowedException
 *
 * @package Wave\Framework\Exceptions
 */
class HttpNotAllowedException extends \Exception
{
    /**
     * @var array List of allowed methods for the given uri
     */
    private $allowed = [];

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Construct the exception. Note: The message is NOT binary safe.
     * @link http://php.net/manual/en/exception.construct.php
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param \Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
     * @param array $allowed
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null, array $allowed = [])
    {
        parent::__construct($message, $code, $previous);
        $this->allowed = $allowed;
    }

    /**
     * Returns array with the allowed methods
     *
     * @return array
     */
    public function getAllowed()
    {
        return $this->allowed;
    }
}
