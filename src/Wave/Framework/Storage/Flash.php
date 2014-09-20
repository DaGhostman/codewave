<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/09/14
 * Time: 23:26
 */

namespace Wave\Framework\Storage;


/**
 * Class Flash
 * @package Wave\Framework\Storage
 *
 * @codeCoverageIgnore
 */
class Flash
{

    const ERROR_MSG = 'error';
    const WARNING_MSG = 'warning';
    const NOTICE_MSG = 'notice';
    const INFO_MSG = 'info';
    const SHARED_MSG = 'shared';

    protected $session = null;
    protected $storage = array(
        'error' => array(),
        'warning' => array(),
        'notice' => array(),
        'info' => array(),
        'shared' => array()
    );


    /**
     * @param $session mixed Session wrapper
     */
    public function __construct($session)
    {
        $this->session = $session;

        $this->storage['error'] = (array) $this->session['flash-error'];
        $this->storage['warning'] = (array) $this->session['flash-warning'];
        $this->storage['info'] = (array) $this->session['flash-info'];
        $this->storage['notice'] = (array) $this->session['flash-notice'];
        $this->storage['shared'] = (array) $this->session['flash-shared'];
    }

    public function add($type, $message)
    {
        if (array_key_exists($type, $this->storage)) {
            array_push($this->storage[$type], (string) $message);
        }

        return $this;
    }

    public function get($type)
    {
        if (array_key_exists($type, $this->storage)) {
            return array_shift($this->storage[$type]);
        }

        return null;
    }

    public function addError($message)
    {
        return $this->add(self::ERROR_MSG, $message);
    }

    public function addInfo($message)
    {
        return $this->add(self::INFO_MSG, $message);
    }

    public function addWarning($message)
    {
        return $this->add(self::WARNING_MSG, $message);
    }

    public function addNotice($message)
    {
        return $this->add(self::NOTICE_MSG, $message);
    }

    public function addShared($message)
    {
        return $this->add(self::SHARED_MSG, $message);
    }

    public function getError()
    {
        return $this->get(self::ERROR_MSG);
    }

    public function getWarning()
    {
        return $this->get(self::WARNING_MSG);
    }

    public function getNotice()
    {
        return $this->get(self::NOTICE_MSG);
    }

    public function getInfo()
    {
        return $this->get(self::INFO_MSG);
    }

    public function getShared()
    {
        return $this->get(self::SHARED_MSG);
    }

    public function __destruct()
    {
        foreach ($this->storage as $type => $value) {
            $this->session['flash-' . $type] = $value;
        }
    }

    public function getAll($type)
    {
        if (!array_key_exists($type, $this->storage)) {
            return array();
        }

        return $this->storage[$type];
    }
}
