<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/controllers/LoginController.php
 */

namespace Application\Controller;

use Application\Controller\AbstractController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Sql;

use Application\Adapter\AuthAdapter;
use Application\Model\Login;
use Application\Form\LoginForm;
use Application\Model\User;
use Application\Model\DbTable\UserTable;


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
	
    public function indexAction() 
    {   	
    	$form = new LoginForm();
    	$request = $this->getRequest();
    	if ($request->isPost())
    	{	
    		$user = new User($this->getAdapter(), new Sql($this->getAdapter()));
			$login = new Login($user);
			$form->setInputFilter($login->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) 
			{
				$data = $form->getData();
				
				$this->getAuthService()->getAdapter()->setIdentity($request->getPost('email'))
									   ->setCredential($request->getPost('password'));
				$result = $this->getAuthService()->authenticate();
				
				$this->getSessionStorage()->setRememberMe(1);
				//set storage again
				$this->getAuthService()->setStorage($this->getSessionStorage());				
				print_r($result);
				
				/*
				$auth = new AuthAdapter($user, $data['email'], $data['password']);
				$login->setAuthAdapter($auth);
				*/
				echo 'fdas';
				exit;
				if($login->processLogin())
				{
					//return $this->redirect()->toRoute('login');
					echo $identity = $auth->getIdentity();
					exit;
					echo 'fdsfdsaa';
					exit;
				}
				
				$this->flashMessenger()->addMessage('Invalid Credentials! Please Try Again');
				return $this->redirect()->toRoute('login');
			}
    	}
    	
    	$view = array();
    	$view['messages'] = $this->flashMessenger()->getMessages();
		$view['title'] = "Login";
		
		//$this->view->headTitle('Login', 'PREPEND');
        $view['form'] = $form;
        return $view;
    }
    
    public function getUsersTable()
    {
    	if (!$this->usersTable) {
    		$sm = $this->getServiceLocator();
    		$this->usersTable = $sm->get('Application\Model\DbTable\UserTable');
    	}
    	return $this->usersTable;
    }    

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index'); // back to login page
    }    
}