<?php
namespace Wave\Framework\Common;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Wave\Framework\Annotation\Inject;

/**
 * @TODO Implement strategy pattern
 * Implementing the pattern will allow using the same object instance to resolve
 * method calls as well as function calls, without using separate objects and etc.
 */
/**
 * Class DependencyResolver
 *
 * @package Wave\Framework\Common
 *
 * Dependency resolving class, currently supports only class dependencies.
 */

class DependencyResolver
{

    protected $parser = null;
    protected $container = null;

    public function __construct($container = null)
    {
        if ($container === null) {
            $container = Container::getInstance();
        }

        $this->container = $container;

        AnnotationRegistry::registerAutoloadNamespace('\\Wave\\Framework\\Annotations\\');
        $this->parser = new AnnotationReader();
    }

    public function verify($class)
    {
        if (is_array($class) || is_callable($class) || is_int($class)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid type provided. Expected string or object, received "%s"', gettype($class))
            );
        }

        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf('Class "%s" does not exist, check the name and try again', $class)
            );
        }

        return $class;
    }

    public function resolve($dependency)
    {
        try {
            $class = $this->verify($dependency);

            $reflection = new \ReflectionClass($class);
            $annotations = $this->parser->getClassAnnotations($reflection);
            $dependencies = [];

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Inject) {
                    echo 'Is Inject';
                    if ($annotation->type === 'Static' && isset($this->container[$annotation->name])) {
                        array_push($dependencies, $this->container[$annotation->name]);
                        continue;
                    } elseif (!isset($this->container[$annotation->name]) && $annotation->type === 'Static') {
                        throw new \RuntimeException(sprintf(
                            'Dependency "%s" was requested as static, but was not in the container',
                            $annotation->name
                        ));
                    } else {
                        if (class_exists($annotation->name)) {
                            $class = $annotation->name;
                            array_push($dependencies, new $class);
                        }
                    }
                }
            }

            return $reflection->newInstanceArgs($dependencies);
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException('Cannot resolve dependency.', null, $e);
        }
    }
}
