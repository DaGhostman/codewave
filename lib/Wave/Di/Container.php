<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 30/07/14
 * Time: 21:46
 */

namespace Wave\Di;


class Container
{
    protected $immutable = array(); // Holds aliases for write-protected definitions
    protected $container = array(); // Holds all the definitions

    private $arguments = array();

    /**
     * @param $alias string Alias to access the definition
     * @param $callback callable The callback which constructs the dependency
     * @param $immutable boolean Can the definition be overridden?
     *
     * @return mixed
     */
    public function register ($alias, $callback, $immutable = false)
    {
        if (in_array($alias, $this->immutable)) {
            return false;
        }

        if ($immutable) {
            array_push($this->immutable, $alias);
        }

        $this->container[$alias] = $this->with(call_user_func($callback), $this)
            ->resolve('\Wave\Di\Dependency');

        return $this;
    }

    /**
     * @param $alias string The alias corresponding to registered callback
     *
     * @return mixed Dependency or null when entry doesn't exist
     */
    public function get ($alias)
    {
        if (!array_key_exists($alias, $this->container)) {
            return null;
        }

        return $this->container[$alias];
    }

    /**
     * Defines non-object parameters which are needed to build the dependency
     *
     * @return $this
     */
    public function with()
    {
        $this->arguments = array_merge($this->arguments, func_get_args());

        return $this;
    }

    public function createReflection($subject)
    {
        $reflection = null;
        if (is_object($subject)) {
            $reflection = new \ReflectionObject($subject);
        } elseif (class_exists($subject, true)) {
            $reflection = new \ReflectionClass($subject);
        }

        return $reflection;
    }

    public function getDependencies($reflection)
    {
        preg_match_all(
            '/@inject\s*(.*?)\n/is',
            $reflection->getDocComment(),
            $matches
        );

        $dependencies = array();
        foreach ($matches[1] as $match) {
            array_push($dependencies, $this->resolve($match));
        }

        return $dependencies;
    }

    public function resolve($subject, $method = null)
    {
        if (($reflection = $this->createReflection($subject)) == null) {
            throw new \RuntimeException(sprintf(
                "Unable to create reflection of subject: %s",
                (is_string($subject) ? $subject : print_r($subject, true))
            ));
        }

        $result = null;
        if ($reflection instanceof \ReflectionObject || $reflection instanceof \ReflectionClass) {

            if ($reflection->hasMethod($method)) {
                $methodReflection = $reflection->getMethod($method);

                $result = $methodReflection->invokeArgs(
                    $subject,
                    array_merge(
                        $this->getDependencies($methodReflection),
                        $this->arguments
                    )
                );
            } elseif (is_string($subject) && class_exists($subject, true)) {
                if ($reflection->getConstructor() != null) {
                    $constructor = $reflection->getConstructor();

                    $result = new Dependency($reflection->newInstanceArgs(array_merge(
                        $this->getDependencies($constructor),
                        $this->arguments
                    )), $this);
                } else {
                    $result = new Dependency($reflection->newInstanceWithoutConstructor(), $this);
                }

            }
        }

        $this->arguments = array();
        return $result;
    }
}
