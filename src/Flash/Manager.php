<?php
/**
 * Created by PhpStorm.
 * User: ddimitrov
 * Date: 17/11/15
 * Time: 15:26
 */

namespace Wave\Framework\Flash;

/**
 * Class Manager
 * @package Wave\Framework\Flash
 */
class Manager
{
    const SUCCESS = 0;
    const INFO = 1;
    const WARN = 2;
    const ERROR = 3;
    const DEBUG = 4;

    /**
     * @var bool
     */
    protected $persistMessages = false;

    /**
     * @var Message[][]
     */
    protected $messages;

    /**
     * Manager constructor.
     * @param $messages
     */
    public function __construct($messages)
    {
        $this->messages = $messages;
    }

    /**
     * Should messages be persisted after retrieval
     *
     * @param bool $state
     * @return $this
     */
    public function persistMessages($state = true)
    {
        $this->persistMessages = $state;

        return $this;
    }

    /**
     * Add a message
     * @param Message $message
     *
     * @return Message
     */
    public function addMessage(Message $message)
    {
        $type = $message->getType();
        if (!array_key_exists($type, $this->messages)) {
            $this->messages[$type] = [];
        }

        if ($message->getId() !== null) {
            $this->messages[$type][$message->getId()] = $message;
        } else {
            $this->messages[$type][] = $message;
        }

        return $this;
    }

    /**
     * Retrieve a message
     *
     * @param mixed $type The type of the messages to retrieve.
     * @param string $id The ID of the message
     * @return null|Message|\Wave\Framework\Flash\Message[]
     */
    public function getMessage($type, $id = null)
    {
        $result = null;
        if (null === $id) {
            if (array_key_exists($type, $this->messages)) {
                $result = $this->messages[$type];

                if (!$this->persistMessages) {
                    unset($this->messages[$type]);
                }
            }
        }

        if (null !== $id) {
            if (array_key_exists($type, $this->messages)) {
                if (array_key_exists($id, $this->messages[$type])) {
                    $result = $this->messages[$type][$id];
                    if (!$this->persistMessages) {
                        unset($this->messages[$type][$id]);
                    }
                }
            }
        }

        return $result;
    }

    public function __destruct()
    {
        if (PHP_SESSION_ACTIVE === session_status()) {
            $_SESSION['_flash'] = [];
            foreach ($this->messages as $type => $messages) {
                $_SESSION['_flash'][$type] = $messages;
            }
        }
    }
}
