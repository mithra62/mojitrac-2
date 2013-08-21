<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/controllers/AbstractController.php
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Storage\Session;
use Zend\Authentication\AuthenticationService;

use Application\Adapter\AuthAdapter;
use Zend\XmlRpc\Value\ArrayValue;
use Zend\Db\Sql\Sql;

 /**
 * Default - AbstractController Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/AbstractController.php
 */
abstract class AbstractController extends AbstractActionController
{
	/**
	 * ZF Config
	 * Contains the entire compiled configuration 
	 * @var Array
	 */
	public $config = array();
	
	protected $adapter;
	
	protected $db;
	
	protected $_flashMessenger = null;
	
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->config = $this->getServiceLocator()->get('Config');	
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		return parent::onDispatch( $e );
	}
	
	public function getAuthService()
	{
		if (! $this->authservice) {
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
	
	public function logoutAction()
	{
		$this->getSessionStorage()->forgetMe();
		$this->getAuthService()->clearIdentity();
	
		$this->flashmessenger()->addMessage("You've been logged out");
		return $this->redirect()->toRoute('login');
	}	
}