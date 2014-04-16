<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
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
					$this->flashMessenger()->addMessage('Please check your email');
					return $this->redirect()->toRoute('forgot-password');
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
    	$hash = $company_id = $this->params()->fromRoute('hash');
    	if(!$hash)
    	{
    		return $this->redirect()->toRoute('forgot-password');
    	}
    	 
    	$fp = $this->getServiceLocator()->get('Application\Model\ForgotPassword');
    	$user_data = $fp->users->getUserByPwHash($hash);
    	if(!$user_data)
    	{
    		return $this->redirect()->toRoute('forgot-password');
    	}

    	$form = $this->getServiceLocator()->get('Application\Form\PasswordForm');
    	$hash = $this->getServiceLocator()->get('Application\Model\Hash');
    
    	$request = $this->getRequest();
    	if ($request->isPost())
    	{
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($fp->users->getPasswordInputFilter($this->identity, $hash, false));
    		$form->setData($formData);    		
    		if ($form->isValid($formData))
    		{
    			$formData = $formData->toArray();
    			if($fp->users->changePassword($user_data['id'], $formData['new_password']))
    			{
    				$this->flashMessenger()->addMessage('Your password hass been reset!');
    				return $this->redirect()->toRoute('login');    				
    			}
    		}
    	}
    
    	$view['form'] = $form;
    	return $view;
    }    
}