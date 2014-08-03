<?php
return array(
    'controllers' => array(
		'invokables' => array(
            'Api\Controller\Activity' => 'Api\Controller\ActivityController',
            'Api\Controller\Admin' => 'Api\Controller\AdminController',
            'Api\Controller\Bookmarks' => 'Api\Controller\BookmarksController',
            'Api\Controller\Calendar' => 'Api\Controller\CalendarController',
            'Api\Controller\Companies' => 'Api\Controller\CompaniesController',
            'Api\Controller\Contacts' => 'Api\Controller\ContactsController',
            'Api\Controller\Docs' => 'Api\Controller\DocsController',
            'Api\Controller\Files' => 'Api\Controller\FilesController',
            'Api\Controller\Import' => 'Api\Controller\ImportController',
            'Api\Controller\Index' => 'Api\Controller\IndexController',
            'Api\Controller\Ips' => 'Api\Controller\IpsController',
            'Api\Controller\Json' => 'Api\Controller\JsonController',
            'Api\Controller\Notes' => 'Api\Controller\NotesController',
            'Api\Controller\Notifications' => 'Api\Controller\NotificationsController',
            'Api\Controller\Options' => 'Api\Controller\OptionsController',
            'Api\Controller\Projects' => 'Api\Controller\ProjectsController',
            'Api\Controller\Reports' => 'Api\Controller\ReportsController',
            'Api\Controller\Roles' => 'Api\Controller\RolesController',
            'Api\Controller\Settings' => 'Api\Controller\SettingsController',
            'Api\Controller\Tasks' => 'Api\Controller\TasksController',
            'Api\Controller\Timers' => 'Api\Controller\TimersController',
            'Api\Controller\Times' => 'Api\Controller\TimesController',
            'Api\Controller\Users' => 'Api\Controller\UsersController',
		),
	),
		
	'router' => array(
		'routes' => array(
			'api' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/api[/][:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Api\Controller\Index',
						'action'     => 'index',
					),
				),
			),
			'projects' => array( //Project Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/api/projects',
        			'defaults' => array(
        				'controller' => 'Api\Controller\Projects',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'all' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/company/[:company_id]',
        					'constraints' => array(
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'index'
        					)
        				)
        			),
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/:project_id',
        					'constraints' => array(
        						'project_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/[:project_id]',
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
        					'route' => '/remove/[:project_id]',
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
        					'route' => '/manage-team/[:project_id]',
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
        					'route' => '/add[/:company_id]',
        					'constraints' => ['company_id' => '[0-9]*'],
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),      			      			
					'chain-projects' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/chain-projects[/:company_id]',
							'constraints' => ['company_id' => '[0-9]*'],
							'defaults' => array(
								'action' => 'chain-projects'
							)
						)
					)
        		),
        	),   //end Project Routes
        	'tasks' => array( //Tasks Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/api/tasks',
        			'defaults' => array(
        				'controller' => 'Api\Controller\Tasks',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'all' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/[:project_id]',
        					'constraints' => array(
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'index'
        					)
        				)
        			), 
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/remove/:task_id',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/add/:project_id',
        					'constraints' => array(
        						'project_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/:task_id',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),       			
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/:task_id',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        			'update-progress' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/update-progress/:task_id/:progress',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'updateProgress'
        					)
        				)
        			),    			      			
					'chain-tasks' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/chain-tasks[/:company_id]',
							'constraints' => ['project_id' => '[0-9]*'],
							'defaults' => array(
								'action' => 'chain-tasks'
							)
						)
					)
        		)
        	), //end Tasks Routes
        			
				
				
		),
	),
    
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),	
);