<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/Module.php
 */

namespace PM;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
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
use PM\Model\Contacts;
use PM\Model\ActivityLog;
use PM\Model\Calendar;
use PM\Model\Ips;
use PM\Model\Users;
use PM\Model\FusionCharts;

use PM\Form\ProjectForm;
use PM\Form\CompanyForm;
use PM\Form\BookmarkForm;
use PM\Form\NoteForm;
use PM\Form\ContactForm;
use PM\Form\TaskForm;
use PM\Form\IpForm;
use PM\Form\ConfirmForm;
use PM\Form\OptionForm;
use PM\Form\TimeForm;
use PM\Form\TimerForm;

use PM\Event\ActivityLogEvent;
use PM\Event\NotificationEvent;

/**
 * PM - Module Object
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/Module.php
 */
class Module implements 
    ConsoleUsageProviderInterface,  // <- our module implement this feature and provides console usage info
    ConsoleBannerProviderInterface
{
	/**
	 * Sets up the module settings
	 * @param ModuleManager $moduleManager
	 */
	public function init(ModuleManager $moduleManager)
	{
		//sets the layout
		$sharedEvents = $moduleManager->getEventManager()->getSharedManager();
		$sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
			$controller = $e->getTarget();
			$controller->layout('layout/pm');
		}, 100);	
	}

	/**
	 * (non-PHPdoc)
	 * @see \Zend\ModuleManager\Feature\ConsoleUsageProviderInterface::getConsoleUsage()
	 */
	public function getConsoleUsage(Console $console)
	{
		return array(
				// Describe available commands
				'archive tasks [--verbose|-v] [--days=] [--status=]',
				array('Updates all tasks to --status that have been marked completed more than --days'),
	
				// Describe expected parameters
				array( '--days',           'How many days you want tasks to be in since Complete status was given' ),
				array( '--status',         'What status you want to set tasks that are Complete past --days' ),
				array( '--verbose|-v',     '(optional) turn on verbose mode'),
				'---------------------------------------',
				'',
				
				'send task reminder [--verbose|-v] [--email=] [--member_id=]',
				array('Sends the Daily Task Reminder email(s). If --email and --member_id are empty everyone gets the email. '),
	
				// Describe expected parameters
				array( '--member_id',      'The member_id for the user you want to trigger' ),
				array( '--email',          'The email address for the user you want to trigger' ),
				array( '--verbose|-v',     '(optional) turn on verbose mode'),
				'---------------------------------------'
		);
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ModuleManager\Feature\ConsoleBannerProviderInterface::getConsoleBanner()
	 */
	public function getConsoleBanner(Console $console)
	{
		return 'PM 2.X';
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
					
					$project = $sm->get('PM\Model\Projects');
					$task = $sm->get('PM\Model\Tasks');
					return new Times($adapter, $db, $project, $task);
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
					$project = $sm->get('PM\Model\Projects');
					$task = $sm->get('PM\Model\Tasks');
					return new Calendar($adapter, $db, $project, $task);
				},
				'PM\Model\Ips' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Ips($adapter, $db);
				},
				'PM\Model\Users' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$role = $sm->get('Application\Model\Roles');
					$ud = $sm->get('Application\Model\User\Data');
					return new Users($adapter, $db, $role, $ud);
				},
				'PM\Model\FusionCharts' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new FusionCharts($adapter, $db);
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
				'PM\Form\IpForm' => function($sm) {
					return new IpForm('ip');
				},
				'PM\Form\ConfirmForm' => function($sm) {
					return new ConfirmForm('confirm');
				},
				'PM\Form\OptionForm' => function($sm) {
					$options = $sm->get('PM\Model\Options');
					return new OptionForm('options', $options);
				},
				'PM\Form\TimeForm' => function($sm) {
					
					$auth = $sm->get('AuthService');
					$perm = $sm->get('Application\Model\Permissions');
					$companies = $sm->get('PM\Model\Companies');
					if($perm->check($auth->getIdentity(), 'view_companies'))
					{
						$types = array('1', '6');
						$options = \PM\Model\Options\Companies::companies($companies, TRUE, FALSE, $types);
					}
					else
					{
						$user = $sm->get('PM\Model\Users');
						$projects = $user->getAssignedProjects($auth->getIdentity());
						$ids = array();
						foreach($projects AS $project)
						{
							$ids[$project['company_id']] = $project['company_id'];
						}
							
						$options = \PM\Model\Options\Companies::companies($companies, TRUE, FALSE, FALSE, $ids);
					}	
					
					return new TimeForm('time', $options);
				},
				'PM\Form\TimerForm' => function($sm) {
					return new TimerForm('timer');
				},
				
				//events
				'PM\Event\ActivityLogEvent' => function($sm) {
				    $auth = $sm->get('AuthService');
				    $al = $sm->get('PM\Model\ActivityLog');
					return new ActivityLogEvent($al, $auth->getIdentity());
				},
				'PM\Event\NotificationEvent' => function($sm) {
				    $auth = $sm->get('AuthService');
				    $mail = $sm->get('Application\Model\Mail');
				    $user = $sm->get('PM\Model\Users');
				    $task = $sm->get('PM\Model\Tasks');
				    $project = $sm->get('PM\Model\Projects');
					return new NotificationEvent($mail, $user, $project, $task, $auth->getIdentity());
				},										
			),
    	);
    }    
}
