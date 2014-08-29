<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/View//Helper/BaseViewHelper.php
 */

namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper as ZFAbstract;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DateTime, IntlDateFormatter, DateInterval;

 /**
 * Base - View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Base/src/Base/View//Helper/BaseViewHelper.php
 */
class BaseViewHelper extends ZFAbstract implements ServiceLocatorAwareInterface 
{
	/**
	 * The users ID
	 * @var unknown
	 */
	public $identity;
	
	/**
	 * The ZF Service Locator object
	 * @var ServiceLocatorInterface
	 */
	public $serviceLocator;
	
	/**
	 * The users custom data including preferences
	 * @var array
	 */
	public $userData;
	
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
	
	public function getUserData()
	{
		if(!$this->userData)
		{
			$helperPluginManager = $this->getServiceLocator();
			$serviceManager = $helperPluginManager->getServiceLocator();
			$ud = $serviceManager->get('Application\Model\UserData');	
			$this->userData = $ud->getUsersData($this->getIdentity());
		}
		return $this->userData;
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
	
	/**
	 * Takes a time stamp (string) and converts it to a different format using date() strings
	 *
	 * @param   string  $oldDate	Original date string
	 * @param   string  $format		Converted date string
	 * @return  string				The new time stamp string
	 */
	function formatDate($oldDate, $format) {
		$newDate = date($format, strtotime($oldDate));
		return $newDate;
	}	
}