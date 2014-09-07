<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 16/08/14
 * Time: 20:35
 */

namespace Wave\Framework\Application;


use Wave\Framework\Application\Contexts\ArgumentsContext;
use Wave\Framework\Application\Interfaces\ControllerInterface;
use Wave\Framework\Storage\Registry;

class Controller implements \Serializable, ControllerInterface
{
    protected $action = null;
    protected $pattern;
    protected $methods = null;

    protected $conditions = array();

    private $arguments = array();

    /**
     * Sets the pattern of the object/controller
     *
     * @param $pattern string The pattern for the object
     * @return \Wave\Framework\Application\Controller
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Sets the controller actions these are in case
     *
     * @param $callback callable A callback
     *
     * @throws \InvalidArgumentException
     *
     * @return \Wave\Framework\Application\Controller
     */
    public function action($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("Invalid callback specified");
        }
        $this->action = $callback;

        return $this;
    }

    /**
     * PHP Magic method __invoke
     *
     * @param $data array Array with arguments to pass
     * @return mixed The result of the call
     */
    public function invoke(array $data = array())
    {
        return call_user_func($this->action, $this->arguments, new Registry(array(
            'mutable' => false,
            'data' => $data
        )));
    }

    /**
     * Defines the methods for which the current route should be valid.
     *
     * @return Controller
     */
    public function via()
    {
        $args = func_get_args();

        if (is_array($args[0])) {
            $this->methods = $args[0];
        } else {
            $this->methods = $args;
        }

        return $this;
    }

    /**
     * Merge route conditions
     *
     * @param array $conditions
     *            Key-value array of URL parameter conditions
     * @return Controller
     */
    public function conditions(array $conditions)
    {
        $this->conditions = array_merge($this->conditions, $conditions);

        return $this;
    }

    /**
     *
     *
     * @param $path string the current request URI
     * @return bool
     */
    public function match($path)
    {
        $conditions = $this->conditions;

        $pattern = preg_replace_callback(
            '#:([\w]+)\+?#',
            function ($match) use ($conditions) {
                if (isset($conditions[$match[1]])) {
                    return '(?P<' . $match[1] . '>' . $conditions[$match[1]] . ')';
                }

                if (substr($match[0], -1) === '+') {
                    return '(?P<' . $match[1] . '>.+)';
                }

                return '(?P<' . $match[1] . '>[^/]+)';
            },
            str_replace(')', ')?', (string) $this->pattern)
        );

        if (substr($this->pattern, - 1) === '/') {
            $pattern .= '?';
        }

        $regex = '#^' . $pattern . '$#i';

        if (preg_match($regex, urldecode($path), $values)) {
            $this->arguments = new ArgumentsContext($this, $values);

            return true;
        }

        return false;
    }

    public function serialize()
    {
        if (!$this->isSerializeable()) {
            return null;
        }

        return serialize(array(
            'pattern' => $this->pattern,
            'methods' => $this->methods,
            'action' => $this->action
        ));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->pattern = $data['pattern'];
        $this->methods = $data['methods'];
        $this->action = $data['action'];
    }

    public function supportsHTTP($method)
    {
        return in_array(strtoupper($method), $this->methods);
    }

    public function isSerializeable()
    {
        return (!$this->action instanceof \Closure);
    }
}
