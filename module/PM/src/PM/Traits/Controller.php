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

namespace PM\Traits;

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

	/**
	 * Setup the Events we're gonna piggyback on
	 * 
	 * Note, we have to implement the other module events since we can't extend the Base\Controller
	 * 
	 * @todo Abstract the registering of events
	 */
	private function _initEvents()
	{
		//setup the Activity Log
		$al = $this->getServiceLocator()->get('PM\Event\ActivityLogEvent');
		$al->register($this->getEventManager()->getSharedManager());
		
		$al = $this->getServiceLocator()->get('PM\Event\NotificationEvent');
		$al->register($this->getEventManager()->getSharedManager());				
	}
}