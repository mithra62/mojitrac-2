<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Controller/BaseController.php
 */

namespace Base\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

 /**
 * Default - AbstractController Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Base/src/Base/Controller/BaseController.php
 */
abstract class BaseController extends AbstractActionController
{
	/**
	 * ZF Config
	 * Contains the entire compiled configuration 
	 * @var Array
	 */
	public $config = array();
	
	/**
	 * The database adapter connection
	 * @var \Zend\Db\Adapter\Adapter
	 */
	protected $adapter;
	
	/**
	 * The actual SQL object for making queries with 
	 * @var object
	 */
	protected $db;
	
	protected $authservice;
	
	protected $storage;
	
	/**
	 * Sets up the Controller defaults
	 * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->config = $this->getServiceLocator()->get('Config');	
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		return parent::onDispatch( $e );
	}
	
	public function getAuthService()
	{
		if (! $this->authservice ) {
			$this->authservice = $this->getServiceLocator()->get('AuthService');
		}
	
		return $this->authservice;
	}
	
	public function getSessionStorage()
	{
		if (! $this->storage) {
			$this->storage = $this->getServiceLocator()->get('Application\Model\Auth\AuthStorage');
		}
	
		return $this->storage;
	}	
	
	public function getAdapter()
	{
		if (!$this->adapter) {
			$sm = $this->getServiceLocator();
			$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
		}
		return $this->adapter;
	}
	
	/**
	 * Wraps up Ajax capable Action returns 
	 * @param array $view
	 * @return \Zend\View\Model\ViewModel|boolean
	 */
	public function ajaxOutput(array $view = array())
	{
	    if ($this->getRequest()->isXmlHttpRequest())
	    {
	    	$result = new ViewModel();
	    	$result->setTerminal(true);
	    	$view['ajax_mode'] = true;
	    	$result->setVariables($view);
	    	return $result;
	    }

	    return $view;
	}
}