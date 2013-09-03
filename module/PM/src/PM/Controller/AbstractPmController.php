<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./moji/application/controllers/AbstractPmController.php
 */

namespace PM\Controller;

use Application\Controller\AbstractController;
use PM\Event\ActivityLogEvent; 

 /**
 * Default - AbstractPmController Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/AbstractPmController.php
 */
abstract class AbstractPmController extends AbstractController
{	
	/**
	 * Session
	 * @var object
	 */
	protected $session;
	
	/**
	 * Permission Object
	 * @var object
	 */
	protected $perm;
	
	/**
	 * Settings array
	 * @var array
	 */
	protected $settings;
	
	/**
	 * Preferences array
	 * @var array
	 */
	protected $prefs;
		
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if( empty($this->identity) )
		{
			return $this->redirect()->toRoute('login');
		}
		
		$settings = $this->getServiceLocator()->get('Application\Model\Settings'); 
		$this->settings = $settings->getSettings();	
		
		$this->_initPrefs();
		$this->perm = $this->getServiceLocator()->get('Application\Model\Permissions');
				
		$this->layout()->setVariable('messages',  $this->flashMessenger()->getMessages());
		//$this->layout()->setVariable('layout_style', 'left');
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('active_nav', 'home');
		$this->layout()->setVariable('sub_menu', 'dashboard');
		$this->layout()->setVariable('identity', $this->identity);
		$this->_initIpBlocker();
		$this->_initEvents();
		
		return parent::onDispatch( $e );
	}

	private function _initEvents()
	{
		//setup the Activity Log
		$hooks = array('project.update.pre' => 'logProjectUpdate');
		$al = new ActivityLogEvent();
		foreach($hooks AS $key => $value)
		{
			$this->getEventManager()
				 ->getSharedManager()
				 ->attach('Application\Model\AbstractModel', $key, array($al, $value));	
		}		
	}
	
	/**
	 * Provides oversight on permission dependant requsts
	 * @param string $permission
	 * @param string $url
	 */
	public function check_permission($permission, $url = FALSE)
	{
		if(!$this->perm->check($this->identity, $permission))
		{
			if(!$url)
			{
				$this->_helper->redirector('index', 'index', 'pm');
			}
			 
			exit;
		}
	}
	
	/**
	 * Start up the IP Blocker
	 */
	protected function _initIpBlocker()
	{
		if(array_key_exists('enable_ip', $this->settings) && $this->settings['enable_ip'] !== FALSE)
		{
			$ip = new PM_Model_Ips;
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