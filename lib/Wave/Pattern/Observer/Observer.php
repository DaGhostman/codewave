<?php

namespace Wave\Pattern\Observer;

use \Wave\Pattern\Observer\Subject;

/** 
 * @author phpAcorn <phpacorn@gmail.com>
 * @copyright phpAcorn 2014
 * @link http://phpacorn/
 * @package phpAcorn\Wave
 * @subpackage Pattern\Observer
 * @version 1.0
 * @name Observer
 * @uses \Wave\Pattern\Observer\Subject
 */
class Observer
{

    /**
     * 
     * Defines the subject and attaches itself to it.
     * @method __construct
     * @access public
     * 
     * @param \Wave\Pattern\Observer\Subject The subject of observation
     * @throws \InvalidArgumentException Invalid subject specified
     */
    public function __construct(Subject $subject)
    {   
        $subject->attach($this);
    }
    
    /**
     * 
     * Triggeres the handlers for the current state
     * of the subject. Passes all arguments to the 
     * handlers with <em>call_user_func_array</em> 
     *
     * @method update
     * @access public
     * 
     * @param mixed $argv all params to pass to the handlers
     * @return object Current object for chaining
     */
    public function update()
    {
        $args = func_get_args();
        $subject = array_shift($args);
        
        if (method_exists($this, $subject->state())) {
            call_user_func_array(
                array($this, $subject->state()),
                $args
            );
        }
        
        return $this;
    }
}
