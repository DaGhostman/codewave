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
     * Register a dependency which can be resolved using the alias.
     *
     * Usage: '@inject $alias' Container::resolve() looks up predefined objects, before
     *          attempting to create all the necessary dependencies.
     *
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

    /**
     * @param $subject mixed The class/object which to reflect
     *
     * @return null|\ReflectionClass|\ReflectionObject
     */
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

    /**
     * Parses object/method's annotations and extracting the dependencies defined in
     *              the annotations
     *
     * @param $reflection mixed An instance of \ReflectionClass or \ReflectionObject
     *
     * @return array array of the dependencies
     */
    public function getDependencies($reflection)
    {
        preg_match_all(
            '/@inject\s*(.*?)\n/is',
            $reflection->getDocComment(),
            $matches
        );

        $dependencies = array();
        foreach ($matches[1] as $match) {
            if (!array_key_exists($match, $this->arguments)) {
                array_push($dependencies, $this->resolve($match, null, true));
            } else {
                array_push($dependencies, $this->arguments[$match]);
            }
        }

        return $dependencies;
    }

    /**
     * Attempts to automatically resolve object/method dependencies and returns them
     *
     * @param mixed $subject Class name or class instance (useful when resolving methods
     *                       of already existing classes)
     * @param mixed $method String if a specific method should get invoked. Default null
     * @param bool $save Should the class get saved in the container for later. Default true
     *
     * @return mixed Dependency object wrapping the resolved object or the result of
     *                       the method execution.
     *
     * @throws \RuntimeException If $subject cannot be reflected (invalid object/class name)
     */
    public function resolve($subject, $method = null, $save = true)
    {

        if (is_string($subject) && array_key_exists($subject, $this->container)) {
            return $this->container[$subject];
        }
        if (($reflection = $this->createReflection($subject)) == null) {
            throw new \RuntimeException(sprintf(
                "Unable to create reflection of subject: %s",
                (is_string($subject) ? $subject : print_r($subject, true))
            ));
        }



        $result = $subject;
        if ($reflection instanceof \ReflectionObject || $reflection instanceof \ReflectionClass) {

            if ($reflection->hasMethod($method)) {
                $result = call_user_func_array(
                    array($this->resolve($subject), $method),
                    $this->getDependencies($reflection->getMethod($method))
                );
            } elseif (is_string($subject) && class_exists($subject, true)) {
                if ($reflection->getConstructor() != null) {
                    $constructor = $reflection->getConstructor();

                    $result = new Dependency($reflection->newInstanceArgs(
                        array_merge($this->getDependencies($constructor), $this->arguments)
                    ), $this);
                } else {
                    $result = new Dependency($reflection->newInstance(), $this);
                }

            }
        }

        $this->arguments = array();

        if ($save && $method == null) {
            $this->container[$reflection->getName()] = $result;
        }

        return $result;
    }
}
