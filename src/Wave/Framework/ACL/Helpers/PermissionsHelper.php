<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 19/09/14
 * Time: 12:45
 */

namespace Wave\Framework\ACL\Helpers;


class PermissionsHelper
{

    protected $path = null;
    protected $extends = array();

    protected $permissions = null;

    /**
     * @param $path string path to the directory containing role files
     * @throws \InvalidArgumentException On invalid directory
     */
    public function __construct($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid directory supplied: %s', $path)
            );
        }

        $this->path = $path;
    }

    /**
     * @param $string string Json string with role definitions
     *
     * @return array
     */
    public function fromString($string)
    {
        $raw = json_decode($string, true);

        $result = $raw['permissions'];

        if (!is_null($this->path)) {
            if (array_key_exists('extends', $raw)) {
                array_push($this->extends, $raw['extends']);

                $result = array_merge($this->fromFile($raw['extends']), $raw['permissions']);
            }
        }

        return array_unique($result);
    }

    /**
     * @param $file string File name without extension
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function fromFile($file)
    {
        $path = sprintf('%s/%s.json', $this->path, $file);

        if (!is_file($path)) {
            throw new \InvalidArgumentException(
                sprintf('File %s not found', $path)
            );
        }
        $raw = json_decode(file_get_contents($path), true);

        $permissions = $raw['permissions'];

        if (array_key_exists('extends', $raw)) {
            array_push($this->extends, $raw['extends']);

            $permissions = array_merge($this->fromFile($raw['extends']), $permissions);
        }

        return array_unique($permissions);
    }

    /**
     * @param $permissions array
     *
     * @return $this
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function inherits($name)
    {
        return in_array($name, $this->extends);
    }
}
