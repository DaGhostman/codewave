<?php


namespace Wave\Application\Observers;

use Wave\Pattern\Observer\Observer;

/**
 * Turns output buffering on and prints
 *          the buffer at the end of execution
 *
 * Class BufferingObserver
 * @package Wave\Application\Observers
 * @deprecated
 */
class BufferingObserver extends Observer
{
    public function mapBefore()
    {
        ob_start();
    }
    
    
    public function applicationAfter()
    {
        echo ob_get_clean();
    }
}
