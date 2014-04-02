<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./moji/application/controllers/LoginController.php
 */

namespace Application\Controller;

use Application\Controller\AbstractController;
use Zend\Authentication\Result as AuthenticationResult;


 /**
 * Default - Login Class
 *
 * Handles login routing 
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/LoginController.php
 */
class LoginController extends AbstractController
{   
	protected $usersTable;
	
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
    		$user = $this->getServiceLocator()->get('Application\Model\User');
			$login = $this->getServiceLocator()->get('Application\Model\Login');
			$login->setAuthAdapter($this->getAdapter());
			$form->setInputFilter($login->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) 
			{
				$data = $form->getData();
				
				$this->getAuthService()->getAdapter()
									   ->setIdentity($request->getPost('email'))
									   ->setCredential($request->getPost('password'));
				
				$result = $this->getAuthService()->authenticate();
				switch ($result->getCode())
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