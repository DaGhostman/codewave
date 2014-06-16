<?php
namespace Wave\Application\Observers;

use Wave\Pattern\Observer\Observer;

class BufferingObserver extends Observer
{
    public function map_before()
    {
        ob_start();
    }
    
    
    public function application_after()
    {
        echo ob_get_clean();
    }
}
