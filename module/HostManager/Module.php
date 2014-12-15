<?php
 /**
 * mithra62 - MojiTrac
 *
 * @subpackage	HostManager
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/Module.php
 */

namespace HostManager;

use Zend\EventManager\EventInterface as Event;
use HostManager\Event\SqlEvent;
use HostManager\Model\Accounts;
use HostManager\Model\Account\Invites;
use HostManager\Model\Users;
use HostManager\Form\SignUpForm;
use HostManager\Form\InviteForm;

use Zend\ModuleManager\ModuleManager;


/**
 * HostManager - Module Object
 *
 * @package 	MojiTrac
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/Module.php
 */
class Module
{		

	/**
	 * Initializes the module 
	 * @param ModuleManager $moduleManager
	 */
	public function init(ModuleManager $moduleManager)
	{
		$this->sharedEvents = $moduleManager->getEventManager()->getSharedManager();
	}
	
	/**
	 * Mostly we're just attaching events  to the system
	 * @param Event $e
	 */
	public function onBootstrap(Event $e)
	{
		$application = $e->getApplication();
		$this->service_manager = $application->getServiceManager();
		$sql_event = $this->service_manager->get('HostManager\Event\SqlEvent');
		$sql_event->register($this->sharedEvents);
	}
	
    public function getConfig()
    {
    	$config = include __DIR__ . '/config/module.config.php';
    	$local_config = __DIR__ . '/config/module.local.config.php';
    	if( file_exists($local_config) )
    	{
    		$local_config = include $local_config;
    		$config = array_merge($config, $local_config);
    	}
    	return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    

    public function getServiceConfig()
    {
    	return array(
			'factories' => array(
				
				//models
				'HostManager\Model\Accounts' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$config = $sm->get('Config');
					$account = new Accounts($adapter, $db);
					$account->setConfig($config);
					return $account;
				},
				'HostManager\Model\Account\Invites' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$config = $sm->get('Config');
					$invite = new Invites($adapter, $db);
					$invite->setConfig($config);
					return $invite;
				},
				'HostManager\Model\Users' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$account = $sm->get('HostManager\Model\Accounts');
					$roles = $sm->get('Application\Model\Roles');
					$ud = $sm->get('Application\Model\UserData');
					$db = $sm->get('SqlObject');
					$user = new Users($adapter, $db, $roles, $ud);
					$user->setAccount($account);
					return $user;
				},

				//forms
				'HostManager\Form\SignUpForm' => function($sm) {
					return new SignUpForm('signup_form');
				},
				'HostManager\Form\InviteForm' => function($sm) {
					return new InviteForm('invite_form');
				},
				
				//events
				'HostManager\Event\SqlEvent' => function($sm) {
					$auth = $sm->get('AuthService');
					$config = $sm->get('Config');
					$account = $sm->get('HostManager\Model\Accounts');
					$sqlEvent = new SqlEvent($auth->getIdentity(), $account, $config);
					return $sqlEvent;
				},
			)
		);
    }
}
