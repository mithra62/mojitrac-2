<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/controllers/ErrorController.php
*/

namespace Application\Controller;

use Application\Controller\AbstractController;

/**
* Default - Error Controller
*
* Handles displaying errors and Exceptions
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/controllers/ErrorController.php
*/
class ErrorController extends AbstractController
{
	/**
	 * Handles notifying devs on app errors
	 * @var LambLib_Service_Notifier_Error
	 */
    private $_notifier;
    
    /**
     * Where Moji is being executed
     * @var string
     */
    private $_environment;

    public function init()
    {
        parent::init();

        $bootstrap = $this->getInvokeArg('bootstrap');

        $environment = $bootstrap->getEnvironment();
        $error = $this->_getParam('error_handler');
        $mailer = new Model_Mail();
        $session = new Zend_Session_Namespace();
        $database = $bootstrap->getResource('db');
        $profiler = $database->getProfiler();

        $this->_notifier = new LambLib_Service_Notifier_Error(
            $environment,
            $error,
            $mailer,
            $session,
            $profiler,
            $_SERVER
        );

        $this->_environment = $environment;
   }	
	
	public function preDispatch()
	{
		$request = $this->getRequest();
		$module = $request->getParam('module');
		if('default' != $module)
		{
			$this->_helper->layout->setLayout($module);
		}
		
		$this->view->layout_style = 'default';
	}

    public function errorAction()
    {
    	LambLib_Controller_Plugin_Cache::$doNotCache = true;
        $errors = $this->_getParam('error_handler');

        $identity = Zend_Auth::getInstance()->getIdentity();
        if($identity)
        {
        	$this->_helper->layout->setLayout('pm');
        }
        
		switch ($errors->type) { 
		    case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
		    case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
		
		        // 404 error -- controller or action not found
		        $this->getResponse()->setHttpResponseCode(404);
		        $this->view->title = 'Page not found';
		        $this->view->headTitle($this->view->title, 'PREPEND');
		        
		    break;
		    default:
		    	
		        // application error 
		        $this->_applicationError();
		        $this->getResponse()->setHttpResponseCode(500);
		        $this->view->title = 'Application error';
		        $this->view->headTitle($this->view->title, 'PREPEND');
		        
		    break;
		}

		$this->view->exception = $errors->exception;
		$this->view->request   = $errors->request;
    }
    
    private function _applicationError()
    {
        $fullMessage = $this->_notifier->getFullErrorMessage();
        $shortMessage = $this->_notifier->getShortErrorMessage();

        switch ($this->_environment) {
            case 'live':
                $this->view->message = $shortMessage;
                break;
            case 'test':
                $this->_helper->layout->setLayout('blank');
                $this->_helper->viewRenderer->setNoRender();

                $this->getResponse()->appendBody($shortMessage);
                break;
            default:
                $this->view->message = nl2br($fullMessage);
        }

        $this->_notifier->notify();
    }

    private function _getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
    

}

