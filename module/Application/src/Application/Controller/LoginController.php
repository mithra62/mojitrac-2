<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Accplication/Controller/LoginController.php
 */

namespace Application\Controller;

use Application\Controller\AbstractController;
use Zend\Authentication\Result as AuthenticationResult;

 /**
 * Application - Login Class
 *
 * Handles login routing 
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/src/Accplication/Controller/LoginController.php
 */
class LoginController extends AbstractController
{  
	/**
	 * Sets up the Login defaults
	 * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$response = parent::onDispatch( $e );
		if($this->identity && $this->params('action') != 'logout')
		{
			return $this->redirect()->toRoute('pm');
		}
		
		return $response;
	}
		
    public function indexAction() 
    {   
    	$form = $this->getServiceLocator()->get('Application\Form\LoginForm');
    	$request = $this->getRequest();
    	if ($request->isPost())
    	{	
    		$user = $this->getServiceLocator()->get('Application\Model\Users');
			$login = $this->getServiceLocator()->get('Application\Model\Login');
			$login->setAuthAdapter($this->getAdapter());
			$form->setInputFilter($login->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) 
			{
				$data = $form->getData();
	
				$email = $request->getPost('email');
				$password = $request->getPost('password');
				$result = $login->procLogin($email, $password, $this->getAuthService());
				switch ($result)
				{
					case AuthenticationResult::SUCCESS:

						$user->upateLoginTime($this->getServiceLocator()->get('AuthService')->getIdentity());
						$this->getSessionStorage()->setRememberMe(1);
						$this->getAuthService()->setStorage($this->getSessionStorage());	
						$this->flashMessenger()->addMessage('Login Successful!');
						
						return $this->redirect()->toRoute('pm');
																
					break;
				
					case AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND:
					case AuthenticationResult::FAILURE_CREDENTIAL_INVALID:
					default:
						$this->flashMessenger()->addMessage('Invalid Credentials! Please Try Again');
						return $this->redirect()->toRoute('login');						
					break;
				}
			}
    	}
    	
    	$view = array();
    	$view['messages'] = $this->flashMessenger()->getMessages();
		$view['title'] = "Login";
		
		//$this->view->headTitle('Login', 'PREPEND');
        $view['form'] = $form;
        return $view;
    }  
}