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
use Zend\Db\TableGateway\TableGateway;

use Application\Adapter\AuthAdapter;
use Application\Model\Login;
use Application\Form\LoginForm;
use PM\Model\Users;

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
    		$user = new Users(new TableGateway($this->getUsersTable()));
			$login = new Login($user);
			$form->setInputFilter($login->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) 
			{
				$data = $form->getData();
				$login->setAuthAdapter(new AuthAdapter($user, $data['email'], $data['password']));
				if($login->processLogin())
				{
					echo 'fdsa';
					exit;
				}
				
				$this->flashMessenger()->set();
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
    		$this->usersTable = $sm->get('PM\Model\Users');
    	}
    	return $this->usersTable;
    }    

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index'); // back to login page
    }    
}