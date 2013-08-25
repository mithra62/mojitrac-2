<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/SettingsController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
* PM - Settings Controller
*
* Routes the Home requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/SettingsController.php
*/
class SettingsController extends AbstractPmController
{
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{ 
		parent::preDispatch();
        $this->view->headTitle('Settings', 'PREPEND'); 
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
		$this->view->active_sub = 'settings';
		$this->view->title = FALSE;	
		$this->view->active_nav = 'none';
	}
    
    /**
     * Handles managing the system emails
     */
    public function emailAction()
    {
        $this->view->sub_menu = 'admin';
        $this->view->active_nav = 'admin';
		$this->view->active_sub = 'emails';
    }
    
    /**
     * The default section page
     */
    public function indexAction()
    {
    	$this->view->sub_menu = 'settings';
    }
    
    /**
     * Handles modifying a password
     */
    public function passwordAction()
    {
    	$this->view->sub_menu = 'settings';
    	$this->view->active_sub = 'password';
    	
    	$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$form = $user->getPasswordForm(array(
            'action' => '/pm/settings/password',
            'method' => 'post',
        ));
        
        if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				if($user->changePassword($this->identity, $formData['new_password']))
				{
			    	$this->_flashMessenger->addMessage('Password changed!');
					$this->_helper->redirector('index','settings');
					exit;		
				}
			}   
		} 

		$this->view->layout_style = 'right';
		$this->view->sidebar = 'dashboard';		
		$this->view->form = $form;
    }
    
    /**
     * Handles the user notifications
     */
    public function notificationsAction()
    {
    	$this->view->sub_menu = 'settings';
    	$this->view->active_sub = 'notifications';
    }
    
    public function prefsAction()
    {
    	$this->view->sub_menu = 'settings';
    	$this->view->active_sub = 'prefs'; 
		$this->view->layout_style = 'right';
		$this->view->sidebar = 'dashboard';
		
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$form = $user->getPrefsForm(array(
            'action' => '/pm/settings/prefs',
            'method' => 'post',
        ));
        
        
        $form->populate($this->prefs);
        if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				$ud = new PM_Model_User_Data;
				if($ud->updateUserData($formData, $this->identity))
				{
			    	$this->_flashMessenger->addMessage('Preferences updated!');
					$this->_helper->redirector('prefs','settings');
					exit;		
				}
			}   
		}        

        $this->view->form = $form;
    }
}