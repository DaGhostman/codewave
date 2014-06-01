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
    protected $subject;
    
    public function __construct(Subject $subject) {
        if (!is_object($subject) || !$subject instanceof Subject) {
            throw new \InvalidArgumentException("Invalid subject provided");
        }
        
        $subject->attach($this);
        $this->subject = $subject;
    }
    
    public function update(){
        if (is_callable($this->subject)) {
            if (method_exists($this, $this->subject->state())) {
                call_user_func_array(array($this, $this->subject->state()), 
                    func_get_args());
            }
        }
        
        return $this;
    }
}
