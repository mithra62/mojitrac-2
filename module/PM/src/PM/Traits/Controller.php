<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Traits/Controller.php
 */

namespace PM\Traits;

/**
 * PM - Controller Trait
 *
 * Contains the global goodies for the PM module
 *
 * @package 	MojiTrac\Traits
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Traits/Controller.php
 */
trait Controller 
{
	/**
	 * Start up the IP Blocker
	 */
	protected function _initIpBlocker()
	{
		if(!empty($this->settings['enable_ip']) && $this->settings['enable_ip'] == '1')
		{
			$ip = $this->getServiceLocator()->get('PM\Model\Ips');
			if(!$ip->isAllowed($_SERVER['REMOTE_ADDR']))
			{
				exit;
			}
		}
	}
	
	/**
	 * Start up the preferences and settings overrides
	 */
	protected function _initPrefs()
	{
		$ud = $this->getServiceLocator()->get('Application\Model\User\Data');
		$this->prefs = $ud->getUsersData($this->identity);
		foreach($this->settings AS $key => $value)
		{
			if(isset($this->prefs[$key]) && $this->prefs[$key] != '')
			{
				$this->settings[$key] = $this->prefs[$key];
			}
			else
			{
				$this->prefs[$key] = $this->settings[$key];
			}
		}
	}
}