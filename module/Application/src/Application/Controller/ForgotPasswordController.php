<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/controllers/ForgotPasswordController.php
 */

/**
 * Include the AbstractController Controller class
 */
include 'AbstractController.php';

 /**
 * Default - Forgot Password Controller Class
 *
 * Handles login routing 
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/ForgotPasswordController.php
 */
class ForgotPasswordController extends Default_Abstract
{
	protected $_flashMessenger = null;
	
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();
    }
    
    public function resetAction()
    {
    	$hash = $this->_request->getParam('p', FALSE);
    	if(!$hash)
    	{
			$this->_helper->redirector('index');
			exit;    		
    	}
    	
    	$user = new PM_Model_Users(new PM_Model_DbTable_Users);
    	$user_data = $user->getUserByPwHash($hash);
    	if(!$user_data)
    	{
			$this->_helper->redirector('index');
			exit;  
    	}
    	
		$form = $user->getPasswordForm(array(
            'action' => '/forgot-password/reset/p/'.$hash,
            'method' => 'post',
        ), FALSE); 
        
        if ($this->getRequest()->isPost()) 
    	{
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				if($user->changePassword($user_data['id'], $formData['new_password']))
				{
					$this->_flashMessenger->addMessage('Your password hass been reset!');
					$this->_helper->redirector('index', 'login');					
				}
			}   
    	}

        $this->view->form = $form;
    }
    
    public function indexAction()
    {	 	
    	$fp = new Model_ForgotPassword;
    	$form = $fp->getForm();
        
    	if ($this->getRequest()->isPost()) 
    	{
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				if($fp->sendEmail($formData['email']))
				{
					$this->_flashMessenger->addMessage('Please check your email');
					$this->_helper->redirector('index', 'login');
				}
			}
    	}
    	$this->view->messages = $this->_flashMessenger->getMessages();
    	$this->view->title = "Forgot Password";
    	$this->view->headTitle($this->view->title, 'PREPEND');
    	$this->view->form = $form;
    }   
}