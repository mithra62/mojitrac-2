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

namespace Application\Controller;

use Application\Controller\AbstractController;

 /**
 * Default - Forgot Password Controller Class
 *
 * Handles login routing 
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/ForgotPasswordController.php
 */
class ForgotPasswordController extends AbstractController
{   
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		return parent::onDispatch( $e );
	}
		
    public function indexAction()
    {
    	$fp = $this->getServiceLocator()->get('Application\Model\ForgotPassword');
    	$form = $this->getServiceLocator()->get('Application\Model\ForgotPasswordForm'); 
    	$request = $this->getRequest();
    	
    	if ($this->getRequest()->isPost()) 
    	{
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($fp->getInputFilter());
    		$form->setData($request->getPost());    		
			if ($form->isValid($formData)) 
			{
				$mail = $this->getServiceLocator()->get('Application\Model\Mail');
				$hash = $this->getServiceLocator()->get('Application\Model\Hash');
				if($fp->sendEmail($mail, $hash, $formData['email']))
				{
					$this->_flashMessenger->addMessage('Please check your email');
					$this->_helper->redirector('index', 'login');
				}
			}
    	}
    	
    	$view = array();
    	$view['messages'] = $this->flashMessenger()->getMessages();
    	$view['title'] = "Forgot Password";
    	
    	//$this->view->headTitle('Login', 'PREPEND');
    	$view['form'] = $form;
    	return $view;    	
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
}