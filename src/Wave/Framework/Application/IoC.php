<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 15/08/14
 * Time: 23:27
 */

namespace Wave\Framework\Application;


use Wave\Framework\DI\Container;

/**
 * Class IoC
 * @package Wave\Framework\Application
 * @deprecated Will be removed in version 3, favoring \Wave\Framework\DI\Container
 */
class IoC extends Container
{
    protected $immutable = array(); // Holds aliases for write-protected definitions
    protected $container = array(); // Holds all the definitions


    /**
     * Register a dependency which can be resolved using the alias.
     *
     * Usage: '@inject $alias' Container::resolve() looks up predefined objects, before
     *          attempting to create a new one with all necessary dependencies.
     *
     * @param $alias string Alias to access the definition
     * @param $callback callable The callback which constructs the dependency
     * @param $immutable boolean Can the definition be overridden?
     *
     * @return mixed
     */
    public function register($alias, $callback, $immutable = false)
    {
        parent::push($alias, $callback);
    }

    /**
     * @param $alias string The alias corresponding to registered callback
     *
     * @return mixed Dependency or null when entry doesn't exist
     */
    public function get($alias)
    {
        return parent::get($alias);
    }

    public function with()
    {
        throw new \BadMethodCallException(
            'The method IoC::with is obsolete. Please consider switching to DI\Container'
        );
    }
}
