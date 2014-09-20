<?php

namespace Wave\Framework\ACL;

/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 17/09/14
 * Time: 23:28
 */

class MAC implements \Serializable
{

    protected $path = null;
    protected $logger = null;

    /**
     * @var \SplObjectStorage
     */
    protected $store = null;

    private static $instance = null;

    private function __construct()
    {
        $this->store = new \SplObjectStorage();
    }

    /**
     * @codeCoverageIgnore
     */
    private function __clone() {
        return null;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance) || !self::$instance instanceof MAC) {
            self::$instance = new MAC();
        }

        return self::$instance;
    }


    public function setPath($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(
                sprintf('%s is not a path', $path)
            );
        }

        $this->path = $path;

        return $this;
    }

    /**
     * @param $object Object
     * @param $role string Name of the role
     * @param $group string Name of the group
     *
     * @return $this
     * @throws \LogicException if group is already assigned
     * @throws \InvalidArgumentException $object is not an object
     */
    public function assign($object, $role = null, $group = null)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException(sprintf('Expected object got %s', gettype($object)));
        }

        if (isset($this->store[$object])) {
            throw new \LogicException('Unable to overwrite object definition');
        }

        $definition = array('role' => null, 'group' => null);



        if ($group) {
            $groupPath = $this->path . DIRECTORY_SEPARATOR . 'groups';
            $component = new Components\Group($groupPath);
            $definition['group'] = $component->setPermissions($component->fromFile($group));
        }

        if ($role) {
            $rolePath = $this->path . DIRECTORY_SEPARATOR . 'roles';
            $component = new Components\Role($rolePath);

            $definition['role'] = $component->setPermissions($component->fromFile($role));
        }




        $this->store->attach($object, $definition);

        return $this;
    }

    /**
     * Returns a clone of the group object. So that objects do not overwrite the group object
     *
     * @param $object object The object of which to get the group
     *
     * @return \Wave\Framework\ACL\Components\Group|null
     */
    public function group($object)
    {
        if (isset($this->store[$object]) &&
            array_key_exists('group', $this->store[$object]) &&
            is_object($this->store[$object]['group'])
        ) {
            return clone $this->store[$object]['group'];
        }

        return null;
    }

    /**
     * Returns a clone of the role object. So that objects do not overwrite the role object
     *
     * @param $object object The object of which to get the role
     *
     * @return \Wave\Framework\ACL\Components\Role|null
     */
    public function role($object)
    {
        if (isset($this->store[$object]) &&
            array_key_exists('role', $this->store[$object]) &&
            is_object($this->store[$object]['role'])
        ) {
            return clone $this->store[$object]['role'];
        }
        return null;
    }

    /**
     * @codeCoverageIgnore
     */
    public function __sleep()
    {
        throw new \LogicException("Serialization of MAC object is not allowed");
    }

    /**
     * @codeCoverageIgnore
     */
    public function serialize ()
    {
        throw new \LogicException('Serialization of MAC object is not allowed');
    }

    /**
     * @codeCoverageIgnore
     */
    public function unserialize ($serialized)
    {
        return;
    }
}
