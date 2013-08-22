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

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;

 /**
 * PM - Global Alerts View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View//Helper/GlobalAlerts.php
 */
class CheckPermission extends AbstractHelper 
{
	public $auth;
	
	function __invoke($permission)
	{
		$view = $this->getView();
		echo $view->identity;
		exit;
		$identity = Zend_Auth::getInstance()->getIdentity();
		return $this->check($identity, $permission);
	}
	
	/**
	 * Set the flash messenger plugin
	 *
	 * @param  PluginFlashMessenger $pluginFlashMessenger
	 * @return FlashMessenger
	 */
	public function setPluginFlashMessenger(PluginFlashMessenger $pluginFlashMessenger)
	{
		$this->pluginFlashMessenger = $pluginFlashMessenger;
		return $this;
	}
	
	/**
	 * Get the flash messenger plugin
	 *
	 * @return PluginFlashMessenger
	 */
	public function getPluginFlashMessenger()
	{
		if (null === $this->pluginFlashMessenger) {
			$this->setPluginFlashMessenger(new PluginFlashMessenger());
		}
	
		return $this->pluginFlashMessenger;
	}
}