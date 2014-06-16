<?php
namespace Wave\Application\Observers;

use Wave\Pattern\Observer\Observer;

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
