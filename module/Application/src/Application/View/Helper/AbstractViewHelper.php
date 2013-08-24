<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View//Helper/GlobalAlerts.php
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper as ZFAbstract;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;

 /**
 * PM - Global Alerts View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View//Helper/GlobalAlerts.php
 */
class AbstractViewHelper extends ZFAbstract implements ServiceLocatorAwareInterface 
{
	public $identity;
	
	public $serviceLocator;
	
	public function getIdentity()
	{
		if (!$this->identity) 
		{
			$helperPluginManager = $this->getServiceLocator();
			$serviceManager = $helperPluginManager->getServiceLocator();
			$this->identity = $serviceManager->get('AuthService')->getIdentity();
		}
		return $this->identity;
	}	
	
	/**
	 * Set the service locator.
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return CustomHelper
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
		return $this;
	}
	/**
	 * Get the service locator.
	 *
	 * @return \Zend\ServiceManager\ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}	
}