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
use DateTime;

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
use PM\Model\Contacts;
use PM\Model\ActivityLog;
use PM\Model\Calendar;

use PM\Form\ProjectForm;
use PM\Form\CompanyForm;
use PM\Form\BookmarkForm;
use PM\Form\NoteForm;
use PM\Form\ContactForm;
use PM\Form\TaskForm;
use PM\Form\UserForm;

use PM\Event\ActivityLogEvent;
use PM\Event\NotificationEvent;

/**
 * PM - Module Object
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/Module.php
 */
class Module
{
	public function init(ModuleManager $moduleManager)
	{
		//sets the layout
		$sharedEvents = $moduleManager->getEventManager()->getSharedManager();
		$sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
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
					
				//models
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
				'PM\Model\ActivityLog' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new ActivityLog($adapter, $db);
				},
				'PM\Model\Contacts' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Contacts($adapter, $db);
				},
				'PM\Model\Calendar' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Calendar($adapter, $db);
				},
				
				//forms
				'PM\Form\ProjectForm' => function($sm) {
					return new ProjectForm('project', $sm->get('PM\Model\Companies'), $sm->get('PM\Model\Options'));
				},	
				'PM\Form\CompanyForm' => function($sm) {
					return new CompanyForm('company');
				},	
				'PM\Form\BookmarkForm' => function($sm) {
					return new BookmarkForm('bookmark');
				},	
				'PM\Form\NoteForm' => function($sm) {
					return new NoteForm('note');
				},	
				'PM\Form\ContactForm' => function($sm) {
					return new ContactForm('contact', $sm->get('PM\Model\Companies'), $sm->get('PM\Model\Options'));
				},
				'PM\Form\TaskForm' => function($sm) {
					return new TaskForm('task', $sm->get('PM\Model\Options'), $sm->get('PM\Model\Projects'));
				},
				'PM\Form\UserForm' => function($sm) {
					return new UserForm('user', $sm->get('PM\Model\Options'), $sm->get('PM\Model\Projects'));
				},
				
				//events
				'PM\Event\ActivityLogEvent' => function($sm) {
				    $auth = $sm->get('AuthService');
				    $al = $sm->get('PM\Model\ActivityLog');
					return new ActivityLogEvent($al, $auth->getIdentity());
				},
				'PM\Event\NotificationEvent' => function($sm) {
				    $auth = $sm->get('AuthService');
				    $al = $sm->get('PM\Model\ActivityLog');
					return new NotificationEvent($al, $auth->getIdentity());
				},	
				'Timezone' => function($sm) {
				    $auth = $sm->get('AuthService');
					$settings = $sm->get('Application\Model\Settings');
					$data = $settings->getSettings();
					date_default_timezone_set($data['timezone']);
					
					$dt = new DateTime();
					$offset = $dt->format('P');
					$settings->query("SET time_zone='$offset'");
					return true;
				},											
			),
    	);
    }    
}
