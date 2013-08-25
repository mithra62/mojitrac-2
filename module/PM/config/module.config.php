<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/ProjectsController.php
*/

return array(
    'router' => array(
        'routes' => array(
            'pm' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/pm',
                    'defaults' => array(
                        'controller' => 'PM\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        	'calendar' => array(
        		'type' => 'Zend\Mvc\Router\Http\Literal',
        		'options' => array(
        			'route'    => '/calendar',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Calendar',
        				'action' => 'index',
        			),
        		),
        	),        		
        	'projects' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/projects[/:company_id]',
        			'constraints' => ['company_id' => '[0-9]*'],
        			'defaults' => array(
        				'controller' => 'PM\Controller\Projects',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'view/[:project_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'edit/[:project_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'remove/[:project_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),  
        			'manage-team' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'manage-team/[:project_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'manageTeam'
        					)
        				)
        			),        			      			
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'add[/:company_id]',
        					'constraints' => ['company_id' => '[0-9]*'],
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			)
        		)
        	),
        	'users' => array(
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/users',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Users',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/view/[:user_id]',
        					'constraints' => array(
        						'user_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'remove/[:user_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'add',
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			)
        		)
        	),
        	
        	'settings' => array( //Settings Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/settings',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Settings',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'password' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'password',
        					'defaults' => array(
        						'action' => 'password'
        					)
        				)
        			),
        			'prefs' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'prefs',
        					'defaults' => array(
        						'action' => 'prefs'
        					)
        				)
        			)
        		)
        	), //end Settings Routes
        	
        	'bookmarks' => array( //Bookmarks Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/bookmarks',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Bookmarks',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'remove/[:bookmark_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'add',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'edit/[:bookmark_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),
        		)
        	), //end Bookmarks Routes
			
        	'companies' => array( //Companies Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/companies',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Companies',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'remove/[:company_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'add',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'edit/[:company_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),        			
        			'map' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'map/[:company_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'map'
        					)
        				)
        			),
        		)
        	), //end Companies Routes

        	'contacts' => array( //Contacts Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/contacts',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Contacts',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'remove/[:contact_id]',
        					'constraints' => array(
        						'contact_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'add/[:company_id]',
        					'constraints' => array(
        						'company_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'edit/[:contact_id]',
        					'constraints' => array(
        						'contact_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),
        		)
        	), //end Contacts Routes
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'PM\Controller\Activity' => 'PM\Controller\ActivityController',
            'PM\Controller\Admin' => 'PM\Controller\AdminController',
            'PM\Controller\Bookmarks' => 'PM\Controller\BookmarksController',
            'PM\Controller\Calendar' => 'PM\Controller\CalendarController',
            'PM\Controller\Companies' => 'PM\Controller\CompaniesController',
            'PM\Controller\Contacts' => 'PM\Controller\ContactsController',
            'PM\Controller\Docs' => 'PM\Controller\DocsController',
            'PM\Controller\Files' => 'PM\Controller\FilesController',
            'PM\Controller\Import' => 'PM\Controller\ImportController',
            'PM\Controller\Index' => 'PM\Controller\IndexController',
            'PM\Controller\Ips' => 'PM\Controller\IpsController',
            'PM\Controller\Json' => 'PM\Controller\JsonController',
            'PM\Controller\Notes' => 'PM\Controller\NotesController',
            'PM\Controller\Notifications' => 'PM\Controller\NotificationsController',
            'PM\Controller\Options' => 'PM\Controller\OptionsController',
            'PM\Controller\Projects' => 'PM\Controller\ProjectsController',
            'PM\Controller\Reports' => 'PM\Controller\ReportsController',
            'PM\Controller\Roles' => 'PM\Controller\RolesController',
            'PM\Controller\Settings' => 'PM\Controller\SettingsController',
            'PM\Controller\Tasks' => 'PM\Controller\TasksController',
            'PM\Controller\Timers' => 'PM\Controller\TimersController',
            'PM\Controller\Times' => 'PM\Controller\TimesController',
            'PM\Controller\Users' => 'PM\Controller\UsersController',

        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/pm'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/pm/header'    => __DIR__ . '/../view/layout/pm/header.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'view_helpers' => array(
	    'invokables' => array(
		    'ActionBlock' => 'PM\View\Helper\ActionBlock',
		    'BackToLink' => 'PM\View\Helper\BackToLink',
		    'BaseUrl' => 'PM\View\Helper\BaseUrl',
		    'Breadcrumb' => 'PM\View\Helper\Breadcrumb',
		    'Calendar' => 'PM\View\Helper\Calendar',
	    	'CheckPermission' => 'PM\View\Helper\CheckPermission',
	    	'CompanyType' => 'PM\View\Helper\CompanyType',
	    	'ConfirmPageUnload' => 'PM\View\Helper\ConfirmPageUnload',
	    	'DashboardTimeline' => 'PM\View\Helper\DashboardTimeline',
	    	'FileSize' => 'PM\View\Helper\FileSize',
	    	'FileStatus' => 'PM\View\Helper\FileStatus',
	    	'FileTypeImage' => 'PM\View\Helper\FileTypeImage',
	    	'FormatDate' => 'PM\View\Helper\FormatDate',
	    	'FormatHtml' => 'PM\View\Helper\FormatHtml',
	    	'FusionCharts' => 'PM\View\Helper\FusionCharts',
	    	'GlobalAlerts' => 'PM\View\Helper\GlobalAlerts',
	    	'InteractIcon' => 'PM\View\Helper\InteractIcon',
	    	'IsDatePast' => 'PM\View\Helper\IsDatePast',
	    	'MakeLink' => 'PM\View\Helper\MakeLink',
	    	'NoteTopic' => 'PM\View\Helper\NoteTopic',
	    	'ProfileMenu' => 'PM\View\Helper\ProfileMenu',
	    	'ProjectPriority' => 'PM\View\Helper\ProjectPriority',	    	
	    	'ProjectStatus' => 'PM\View\Helper\ProjectStatus',
	    	'ProjectType' => 'PM\View\Helper\ProjectType',
	    	'RelativeDate' => 'PM\View\Helper\RelativeDate',
	    	'TaskPriority' => 'PM\View\Helper\TaskPriority',
	    	'TaskStatus' => 'PM\View\Helper\TaskStatus',
	    	'TaskType' => 'PM\View\Helper\TaskType',
	    	'Truncate' => 'PM\View\Helper\Truncate',
	    	'UserInfo' => 'PM\View\Helper\UserInfo',
	    ),
    ),    
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
