<?php
/**
 * Created by PhpStorm.
 * User: dagho_000
 * Date: 14/01/2015
 * Time: 03:00
 */

namespace Wave\Framework\Application;


use Wave\Framework\Event\Emitter;
use Zend\Log\Logger;
use Zend\Log\Writer\FirePhp;
use Zend\Log\Writer\FirePhp\FirePhpBridge;

class Debugger
{
    private static $logger = null;

    public static function getLogger()
    {
        if (!self::$logger) {
            $writer = new FirePhp(new FirePhpBridge(\FirePHP::getInstance(true)));
            $logger = new Logger();
            $logger->addWriter($writer);

            self::$logger = $logger;
        }

        return self::$logger;
    }

    public function __construct()
    {
        ob_start();
        $e = Emitter::getInstance();
        /**
         * Register the not found handler for the debugger
         */
        $e->on(
            'route_notFound',
            array($this, 'routeNotFoundHandler')
        );

        $e->on(
            'route_badMethod',
            array($this, 'routeMethodNotAllowedHandler')
        );

        $e->on(
            'route_called',
            array($this, 'routeDispatchedHandler')
        );
    }

    public static function breakpoint()
    {
        try {
            throw new \Exception(func_get_arg(0) ?: 'Breakpoint');
        } catch (\Exception $e) {
            self::getLogger()
                ->notice(sprintf('Breakpoint \'%s\' in %s:%s', $e->getMessage(), __FILE__, __LINE__));


            $trace = array();
            foreach ($e->getTrace() as $index => $tr) {
                array_push($trace, sprintf(
                    '#%s. %s%s%s(%s) in %s:%s',
                    $index,
                    $tr['class'],
                    $tr['type'],
                    $tr['function'],
                    implode(',', $tr['args']),
                    $tr['file'] ?: 'unknown',
                    $tr['line'] ?: 'unknown'
                ));
            }
            self::getLogger()
                ->info("", $trace);

            ob_end_flush();
            exit;
        }
    }

    public function routeNotFoundHandler($event)
    {
        self::getLogger()->warn(
            sprintf(
                "No route found for URI: %s",
                $event['uri']
            )
        );
        self::getLogger()->info(
            '',
            $event['data']['request']->toArray()
        );
    }

    public function routeMethodNotAllowedHandler($event)
    {
        self::getLogger()->crit(sprintf(
            "Method %s not allowed. Allowed: %s",
            strtoupper($event['method']),
            implode(',', $event['data']['methodsAllowed'])
        ));
        self::getLogger()->info('', $event['data']['request']->toArray());
    }

    public function routeDispatchedHandler($event)
    {

    }
}
