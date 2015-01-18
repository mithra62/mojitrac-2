<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Cron/BaseCron.php
 */

namespace Base\Cron;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Cron\CronExpression;

abstract class BaseCron implements ServiceLocatorAwareInterface
{

	/**
	 * The ZF Service Locator object
	 * @var ServiceLocatorInterface
	 */
	public $serviceLocator;
	
	abstract public function shouldRun();
	
	abstract public function run();
	
	public function setRunDate()
	{
		
	}
	
	public function isDue()
	{
		$cron = CronExpression::factory('@daily');
		$cron->isDue();
		echo $cron->getNextRunDate()->format('Y-m-d H:i:s');		
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