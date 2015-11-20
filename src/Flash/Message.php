<?php
/**
 * Created by PhpStorm.
 * User: ddimitrov
 * Date: 17/11/15
 * Time: 13:53
 */

namespace Wave\Framework\Flash;

/**
 * Class Message
 * @package Wave\Framework\Flash
 */
class Message implements \Serializable
{
    const SUCCESS = 0;
    const INFO = 1;
    const WARN = 2;
    const ERROR = 3;
    const DEBUG = 4;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var int
     */
    protected $type;

    /**
     * @var array|null
     */
    protected $context;

    /**
     * @var string
     */
    protected $id;

    /**
     * Message constructor.
     * @param string $id
     * @param string $message
     * @param array|null $context
     * @param int $type
     */
    public function __construct($id, $message, array $context = null, $type = 1)
    {
        $this->id = $id;
        $this->message = $message;
        $this->type = $type;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return Message
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Message
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     * @return Message
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->type === self::SUCCESS;
    }

    /**
     * @return bool
     */
    public function isInfo()
    {
        return $this->type === self::INFO;
    }

    /**
     * @return bool
     */
    public function isWarning()
    {
        return $this->type === self::WARN;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->type === self::ERROR;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->type === self::DEBUG;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            'message' => $this->message,
            'type' => $this->type,
            'context' => is_array($this->context) ? $this->context : null
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $item => $value) {
            $this->$item = $value;
        }
    }
}
