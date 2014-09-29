<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 26/09/14
 * Time: 13:38
 */

namespace Wave\Framework\DI;


class Parser
{
    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @var array Assoc array with variable name as key and dependency as value
     */
    protected $injects = array();

    public function __construct($container, $docblock)
    {
        $this->setContainer($container);
        $regex = '#@[a-zA-Z]{3,}\s*[\\a-zA-Z]{1,}\s*[\$a-zA-Z0-9_]{1,}#is';

        preg_match_all($regex, $docblock, $matches);

        foreach ($matches as $match) {

            preg_match('#^@[a-zA-Z ]{1,}#is', $match[0], $type);


            if (!empty($type) && strtolower(rtrim(ltrim($type[0], '@'))) == 'inject') {
                    call_user_func_array(
                        array($this, 'inject'),
                        array_reverse(explode(' ', ltrim($match[0], '@inject ')))
                    );
            }

        }
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function inject($variable, $className)
    {
        $this->injects[ltrim($variable, '$')] = $this->container->resolve($className);
    }

    public function getDependencies()
    {
        return $this->injects;
    }
}
