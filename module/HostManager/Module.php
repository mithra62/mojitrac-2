<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @subpackage	HostManager
 * @author		Eric Lamb
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
 * @package 	mithra62:Mojitrac
 * @subpackage	HostManager
 * @author		Eric Lamb
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
							$adapter = $sm->get('Zend\Db\Adapter\Adapter');
							$db = $sm->get('SqlObject');
														
							return new SqlEvent($auth->getIdentity());
						},
						'HostManager\Model\Accounts' => function($sm) {
							$auth = $sm->get('AuthService');
							$adapter = $sm->get('Zend\Db\Adapter\Adapter');
							$db = $sm->get('SqlObject');							
							return new Accounts($adapter, $db);
						},
					)
			);
    }
}
