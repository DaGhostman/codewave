<?php

namespace Root;

class test {
    protected $arguments = array();

    public function with()
    {
        $this->arguments = func_get_args();

        return $this;
    }

    public function resolve($object, $method = null)
    {

        $arguments = $this->arguments;

        if (method_exists($object, $method)) {
            $reflection = new \ReflectionMethod($object, $method);

            preg_match_all(
                '#@inject:\s*(.*?)\n#is',
                $reflection->getDocComment(),
                $annotations
            );

            $dependencies = array();
            foreach ($annotations[1] as $dependency) {
                array_push($dependencies, $this->resolve($dependency));
            }

            return call_user_func_array(
                array($object, $method),
                array_merge($dependencies, $arguments)
            );

        } elseif (class_exists($object, true)) {


            $r = new \ReflectionClass($object);

            var_dump("Can invoke it", $r->isInstantiable());
            if (($reflection = $r->getConstructor()) != null && $r->isInstantiable()) {


                $doc = $reflection->getDocComment();

                //'/(extension|filter)\:([a-z]{1,})\s*\(([^\)]*)\)(:?\@(.*))?/i'
                preg_match_all('#@inject:\s*(.*?)\n#is', $doc, $annotations);

                print '2' . $r->getName() . PHP_EOL;
                $dependencies = array();
                foreach ($annotations[1] as $dependency) {
                    array_push($dependencies, $this->resolve($dependency));
                }

                return $r->newInstanceArgs($dependencies);
            } else {
                return $r->newInstanceWithoutConstructor();
            }

        }
    }
}

class FooBar
{
    /**
     * @param $option
     *
     * @inject: Root\Baz
     */
    public function test($option)
    {
        if (!is_object($option)) {
            throw new \Exception("Didn't get the object");
        } elseif ($option instanceof Baz) {
            print "Long Live the King!";
        }
    }
}

class A
{
    public function getName()
    {
        return 'Dependency\'s dependency';
    }
}

class Baz
{
    /**
     * @inject: Root\A
     */
    public function __construct($arg = null)
    {
        print 'The king is dead! ';


        if (is_object($arg)) {

            echo $arg->getName();
        }
    }
}


$foo = new FooBar;
$test = new test;
$test->resolve($foo, 'test');

$method = new \ReflectionMethod($foo, 'test');
