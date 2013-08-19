<?php
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class Zend_View_Helper_ProfileMenu
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function profileMenu()
    {
    	if(!isset($this->view->user_data))
    	{
    		$this->view->user_data = 'fdsa'	;
    	}
		Zend_Auth::getInstance()->hasIdentity();
		$identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            //$return  = '<li><a href="'.$this->view->url(array('controller'=>'settings','action'=>'index')).'">Settings</a></li>';
            $return = '<li><a href="'.$this->view->url(array('module'=>'jt', 'controller'=>'index','action'=>'index')).' " target="_blank">Dashboard</a></li>';            
            $return .= '<li><a href="'.$this->view->url(array('controller'=>'login','action'=>'logout')).'">Logout</a></li>';
        } else {
        	$return = '<li><a href="'.$this->view->url(array('controller'=>'login','action'=>'index')).'">Login</a></li>';
        	$return .= '<li><a href="'.$this->view->url(array('controller'=>'signup','action'=>'index')).'">Signup</a></li>';
        }
        
        return $return;
    }
}