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

/**
 * Class Debugger
 * @package Wave\Framework\Debug
 *
 *
 * @codeCoverageIgnore
 *
 */
class Debugger implements LoggerAwareInterface
{
    private $logger = null;

    public function __construct($logger)
    {
        $this->setLogger($logger);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function breakpoint($string, $data = [])
    {
        try {
            throw new \Exception($string ?: 'Breakpoint');
        } catch (\Exception $e) {
            $this->logger->notice(sprintf(
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
                    isset($tr['file']) ? $tr['file']: 'unknown',
                    isset($tr['line']) ? $tr['line']: 'unknown'
                ));
            }
            $this->logger->info("", $trace);

            return ob_end_flush();
        }
    }
}
