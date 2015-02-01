<?php
/**
 * Created by PhpStorm.
 * User: dagho_000
 * Date: 14/01/2015
 * Time: 03:00
 */

namespace Wave\Framework\Debug;


use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

class Debugger implements LoggerAwareInterface
{
    protected $name = null;
    private static $logger = null;
    private static $instance = null;

    public static function getInstance($name = 'default')
    {
        if (null === self::$instance) {
            self::$instance = new Debugger($name);
        }

        return self::$instance;
    }

    public function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    protected function __construct()
    {
        ob_start();
    }

    public static function breakpoint($string, $data = [])
    {
        try {
            throw new \Exception($string ?: 'Breakpoint');
        } catch (\Exception $e) {
            self::$logger->notice(sprintf(
                'Breakpoint \'%s\' in %s:%s',
                $e->getMessage(),
                __FILE__,
                __LINE__
            ), $data);


            $trace = [];
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
            self::$logger->info("", $trace);

            ob_end_flush();
            exit;
        }
    }
}
