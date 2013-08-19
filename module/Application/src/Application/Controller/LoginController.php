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

use Application\Model\Login;
use Application\Form\LoginForm;

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
    public function indexAction()
    {   	
    	$form = new LoginForm();
    	$request = $this->getRequest();
    	if ($request->isPost())
    	{
			$login = new Login();
			$form->setInputFilter($login->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) 
			{
				echo 'fdsa';
				exit;
			}
    	}
    	
    	$view = array();
    	$view['messages'] = $this->flashMessenger()->getMessages();
		$view['title'] = "Login";
		
		//$this->view->headTitle('Login', 'PREPEND');
        $view['form'] = $form;
        return $view;
    }

    public function processAction()
    {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->getLoginForm();
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            $this->view->messages = array('Invalid Credentials! Please Try Again :)');
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
		// inside of AuthController / loginAction
		$values = $this->_request->getPost();
		///$adapter = new AuthAdapter($values['email'],$values['password']);
				
		$auth = Zend_Auth::getInstance();
		
		// Set up the authentication adapter
		$authAdapter = new LambLib_Adapter_Auth($values['email'],$values['password']);
		
		// Attempt authentication, saving the result
		$result = $auth->authenticate($authAdapter);
		
		switch ($result->getCode()) 
		{
		    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
            	/** do stuff here **/
		        break;
		
		    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
	            // Invalid entries
	            $this->view->messages = array('Invalid Credentials! Please Try Again :)');
	            
	            $this->view->form = $form;
	            $form->populate($values);
	            return $this->render('index'); // re-render the login form
		        break;
		
		    case Zend_Auth_Result::SUCCESS:
		        /** do stuff for successful authentication **/
		    	
		    	//update 
		        break;
		
		    default:
		        /** do stuff for other failure **/
		        break;
		}
		
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$user->upateLoginTime($auth->getIdentity());

        // We're authenticated! Redirect to the home page
        
		$this->session = new Zend_Session_Namespace('PM');
		$this->_flashMessenger->addMessage('Welcome to MojiTrac!');
		if($this->session->redirect_to != '')
		{	
			$url = $this->session->redirect_to;
			unset($this->session->redirect_to);
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->gotoUrl($url);
			exit;
		}
		else
		{
			$this->_helper->redirector('index', 'index', 'pm');
		}
		exit;
        
    }


    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index'); // back to login page
    }    
}