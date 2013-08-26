<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/Module.php
 */

namespace PM;

use Zend\ModuleManager\ModuleManager;

use PM\Model\Projects;
use PM\Model\Companies;
use PM\Model\Timers;
use PM\Model\Charts;
use PM\Model\Files;
use PM\Model\Tasks;
use PM\Model\Times;
use PM\Model\Bookmarks;
use PM\Model\Notes;
use PM\Model\Options;
use PM\Model\Notifications;
use PM\Form\ProjectForm;

/**
 * PM - Module Object
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/Module.php
 */
class Module
{
	/**
	 * Sets the layout to use the PM special one
	 * @param ModuleManager $moduleManager
	 */
	public function init(ModuleManager $moduleManager)
	{
		$sharedEvents = $moduleManager->getEventManager()->getSharedManager();
		$sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
			// This event will only be fired when an ActionController under the MyModule namespace is dispatched.
			$controller = $e->getTarget();
			$controller->layout('layout/pm');
		}, 100);
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
				'PM\Model\Projects' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Projects($adapter, $db);
				},
				'PM\Model\Companies' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Companies($adapter, $db);
				},				
				'PM\Model\Timers' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Timers($adapter, $db);
				},
				'PM\Model\Charts' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Charts($adapter, $db);
				},
				'PM\Model\Files' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Files($adapter, $db);
				},
				'PM\Model\Tasks' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Tasks($adapter, $db);
				},
				'PM\Model\Times' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Times($adapter, $db);
				},
				'PM\Model\Bookmarks' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Bookmarks($adapter, $db);
				},
				'PM\Model\Notes' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Notes($adapter, $db);
				},
				'PM\Model\Options' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Options($adapter, $db);
				},
				'PM\Model\Notifications' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Notifications($adapter, $db);
				},
				'PM\Form\ProjectForm' => function($sm) {
					return new ProjectForm('project', $sm->get('PM\Model\Companies'), $sm->get('PM\Model\Options'));
				},											
			),
    	);
    }    
}
