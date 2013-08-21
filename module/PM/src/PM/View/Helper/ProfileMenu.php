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
        	$_user_data = new Model_DbTable_Member;
        	$user_data = $_user_data->getMember(" id = '$identity'");
            $username = $user_data['first_name'].' '.$user_data['last_name'];
            $return  = '<p>Welcome Back <a href="'.$this->view->url(array('module'=> 'pm', 'controller'=>'users','action'=>'view', 'id' => $identity), null, TRUE).'" class="highlight">'.$username.'</a> | ';
            $return .= ' <a href="'.$this->view->url(array('module'=> 'pm', 'controller'=>'calendar','action'=>'index'), null, TRUE).'" class="highlight">Calendar</a> | ';
            
            $return .= ' <a href="'.$this->view->url(array('module'=> 'pm', 'controller'=>'settings','action'=>'index'), null, TRUE).'" class="highlight">Settings</a>';
            $return .=' | <a href="'.$this->view->url(array('module'=> 'default', 'controller'=>'login','action'=>'logout'), null, TRUE).'" class="highlight">Logout</a></p>';
        } else {
        	$return = '<a href="'.$this->view->url(array('controller'=>'login','action'=>'index')).'">Login</a>';
        	$return .= ' <a href="'.$this->view->url(array('controller'=>'signup','action'=>'index')).'">Signup</a>';
        }
        
        return $return;
    }
}