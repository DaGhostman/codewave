<?php
/**
 * Created by PhpStorm.
 * User: daghostman
 * Date: 14/09/14
 * Time: 00:15
 */

namespace Tests\Application;

use Wave\Framework\Application\Module;

class CoreMock {
    private $controllers = array();

    public function controller()
    {
        array_push($this->controllers, func_get_args());
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public function clearControllers()
    {
        $this->controllers = array();
    }
}

class ControllerMock
{
    public function method() {}
}

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    private $tmp = null;
    public function setUp()
    {
        if (is_writable(sys_get_temp_dir()) && !is_file(sys_get_temp_dir().'/module.xml')) {
            touch(sys_get_temp_dir().'/module.xml');
            $xml = '<?xml version="1.0" encoding="UTF-8"?><routes><route controller="\Tests\Application\ControllerMock" method="method" pattern="/" via="GET"></route></routes>';

            file_put_contents(sys_get_temp_dir().'/module.xml', $xml);
        }
    }

    public function testModuleControllersGeneration()
    {
        $core = new CoreMock();
        $controllers = $core->getControllers();
        $module = new Module($core, 'module', sys_get_temp_dir(), '/root');

        $this->assertEquals(
            array(
                      '/root/',
                      array('GET'),
                      array(new \Tests\Application\ControllerMock(), 'method'),
                      array(),
                      '\Wave\Framework\Application\Controller'
            ),
            $controllers[1]
        );
    }
}
