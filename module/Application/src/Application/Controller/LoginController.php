<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
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
 * @package 	Users\Login
 * @author		Eric Lamb <eric@mithra62.com>
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
		
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
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
						$this->flashMessenger()->addMessage($this->translate('login_successful', 'app'));
						
						return $this->redirect()->toRoute('pm');
																
					break;
				
					case AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND:
					case AuthenticationResult::FAILURE_CREDENTIAL_INVALID:
					default:
						$this->flashMessenger()->addMessage($this->translate('invalid_credials_try_again', 'app'));
						return $this->redirect()->toRoute('login');						
					break;
				}
			}
			else
			{
				
			}
    	}
    	
    	$view = array();
    	$view['messages'] = $this->flashMessenger()->getMessages();
        $view['form'] = $form;
        return $view;
    }  
}