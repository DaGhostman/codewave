<?php
/**
 * Created by PhpStorm.
 * User: dagho_000
 * Date: 14/01/2015
 * Time: 03:00
 */

namespace Wave\Framework\Application;


class Debugger
{
    public function __construct()
    {
        ob_start();
    }

    public static function breakpoint()
    {
        try {
            throw new \Exception('Breakpoint');
        } catch (\Exception $e) {
            echo sprintf('<em>Breakpoint reached in <strong>%s</strong>: %s</em><br />' . PHP_EOL, __FILE__, __LINE__);
            echo sprintf('<em>Memory used by script: <strong>%s MB</strong> </em><br />' . PHP_EOL, self::getMemory());
            echo sprintf('<em>Memory used by PHP: <strong>%s MB</strong> </em><br />' . PHP_EOL, self::getSystemMemory());
            echo sprintf('<pre><code>%s</code></pre>', $e->getTraceAsString());
            ob_end_flush();
            exit(0);
        }
    }

    public function routeNotFoundHandler($e)
    {
        echo sprintf("No route found for URI: <strong>%s</strong>:<em>%s</em><br />" . PHP_EOL, $e['data']['method'], $e['data']['uri']);
        echo sprintf("Request Data: <br />");
        echo sprintf(var_export($e['data']['request'], true));
    }

    public function routeMethodNotAllowedHandler($e)
    {
        echo sprintf("Request method <strong>%s</strong> not allowed for route: <em>%s</em><br />" . PHP_EOL, $e['data']['method'], $e['data']['uri']);
        echo sprintf("Allowed methods: <em>%s</em><br />", implode($e['data']['methodsAllowed']));
        echo sprintf("Request Data: <br />");
        echo sprintf(var_export($e['data']['request'], true));
    }

    public function routeDispatched($e)
    {

    }

    public static function getMemory($bytes = false)
    {
        if ($bytes == false) {
            return memory_get_usage();
        }

        return (memory_get_usage()/1024)/1024;
    }

    public static function getSystemMemory($bytes = false)
    {
        if ($bytes == false) {
            return memory_get_usage(true);
        }

        return (memory_get_usage(true)/1024)/1024;
    }
}