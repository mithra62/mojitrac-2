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
        return include __DIR__ . '/config/module.config.php';
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
				'HostManager\Event\SqlEvent' => function($sm) {
					$auth = $sm->get('AuthService');
					$config = $sm->get('Config');
					$account = $sm->get('HostManager\Model\Accounts');
					$sqlEvent = new SqlEvent($auth->getIdentity(), $account, $config['sub_primary_url']);
					return $sqlEvent;
				},
				'HostManager\Model\Accounts' => function($sm) {
					$auth = $sm->get('AuthService');
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');	
					$account = new Accounts($adapter, $db);
					return $account;
				},
			)
		);
    }
}
