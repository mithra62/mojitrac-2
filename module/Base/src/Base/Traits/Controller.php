<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Controller/BaseController.php
 */

namespace Base\Traits;

trait Controller 
{
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
}