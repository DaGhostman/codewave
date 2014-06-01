<?php
namespace Wave\Pattern\Observer;

use Wave\Pattern\Observer\Observer;

/** 
 * @author phpAcorn <phpacorn@gmail.com>
 * @copyright phpAcorn 2014
 * @link http://phpacorn/
 * @package phpAcorn\Wave
 * @subpackage Pattern\Observer
 * @version 1.0
 * @name Subject
 * @uses \Wave\Pattern\Observer\Observer
 */
class Subject
{
    /**
     * @var array $observers The pool of observers
     */
    protected $observers = array();
    
    /**
     * @var string $state The state of the subject, defaults to null
     */
    protected $state = null;
    
    /**
     * Attaches observer to the pool
     * 
     * @method attach
     * @access public
     * @param \Wave\Pattern\Observer\Observer $observer Observer to add
     * @return object Object for chaining
     */
    public function attach(Observer $observer)
    {
        if (false === $this->hasObserver($observer)) {
            array_push($this->observers, $observer);
        }
        
        return $this;
    }
    
    /**
     * Removes an observer from the pool
     * 
     * @method detach
     * @access public
     * @param \Wave\Pattern\Observer\Observer $observer Observer to remove
     * @return object Object for chaining
     * @throws \ErrorException Observer not existing, empty pool
     */
    public function detach (Observer $observer)
    {
        if (!empty($this->observers)) {
            if (false !== ($key = $this->hasObserver($observer))) {
                unset($this->observers[$key]);
            } else {// It doesn't exist
                throw new \ErrorException("Trying to remove undefined observer");
            }
        }
        
        return $this;
    }
    
    /**
     * Search for a given observer. array_search wrapper
     * 
     * @method hasObserver
     * @access protected
     * @param \Wave\Pattern\Observer\Observer $observer Observer to lookup
     * @return mixed False or observer key if exists
     */
    public function hasObserver(Observer $observer)
    {
        return array_search($observer, $this->observers);
    }
    
    /**
     * Notify observers for change in subject's state.
     * All arguments passed to it are redirected to each
     * observer, @see call_user_func_array.
     * 
     * @method notify
     * @access public
     * @param mixed $params Arguments to pass to each observer
     */
    public function notify()
    {
        $shift = array_reverse(func_get_args());
        array_push($shift, $this);
        $args = array_reverse($shift);
        
        foreach ($this->observers as $observer) {
            call_user_func_array(array($observer,'update'), $args);
        }
    }
    
    /**
     * 
     * Representing the call to <em>state</em> getter/setter 
     *
     * @method state
     * @access public
     * @param string $method The called method, namely <em>state</em>
     * @param mixed $args Arguments passed to the method, the new state
     * 
     * @return mixed On success Object for chaining
     */
    public function state($state = null)
    {
        if ($state === null) {
            return $this->state;
        } elseif ($state !== null && is_string($state)) {
            $this->state = $state;
            
        }
        return $this;
    }
}
