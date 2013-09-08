<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/config/module.config.php
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

        	'admin' => array( //Admin Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/admin',
        			'constraints' => array(
        				'id' => '[0-9]+'
        			),
        			'defaults' => array(
        				'controller' => 'PM\Controller\Admin',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'settings' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/settings',
        					'defaults' => array(
        						'action' => 'settings'
        					)
        				)
        			),
        			'system-reset' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/system-reset',
        					'defaults' => array(
        						'action' => 'systemReset'
        					)
        				)
        			),
        		)
        	), //end Admin Routes

        	'bookmarks' => array( //Bookmarks Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/bookmarks',
        			'constraints' => array(
        				'id' => '[0-9]+'
        			),
        			'defaults' => array(
        				'controller' => 'PM\Controller\Bookmarks',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'all' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/[:type][/:id]',
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
        					'route' => '/remove/[:bookmark_id]',
        					'constraints' => array(
        						'bookmark_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/add[/:type][/:id]',
        					'constraints' => array(
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/[:bookmark_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),       			
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/view/[:bookmark_id]',
        					'constraints' => array(
        						'bookmark_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
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
        					'route' => '/remove/[:company_id]',
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
        					'route' => '/add',
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
        					'route' => '/edit/[:company_id]',
        					'constraints' => array(
        						'slug' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),      			
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/view/[:company_id]',
        					'constraints' => array(
        						'company_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),           			
        			'map' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/map/:company_id',
        					'constraints' => array(
        						'company_id' => '[0-9]+'
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
        			'all' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/[:company_id]',
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
        					'route' => '/remove/[:contact_id]',
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
        					'route' => '/add/[:company_id]',
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
        					'route' => '/edit/[:contact_id]',
        					'constraints' => array(
        						'contact_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),       			
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/view/[:contact_id]',
        					'constraints' => array(
        						'contact_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        		)
        	), //end Contacts Routes

			'docs' => array( //Docs Routes
				'type' => 'segment',
				'options' => array(
					'route' => '/pm/docs[/:action][/:type][/:page]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*', 
						'type' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'page' => '[a-zA-Z][a-zA-Z0-9_-]*',
					),
					'defaults' => array(
						'controller' => 'PM\Controller\Docs',
						'action' => 'index',
					),
				),
			), //end Docs Routes

        	'files' => array( //Files Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/files',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Files',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/remove/[:file_id]',
        					'constraints' => array(
        						'file_id' => '[0-9]+'
        					),
        					'defaults' => array( 
        						'controller' => 'PM\Controller\Files',
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/add/[:company_id]',
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
        					'route' => '/edit/[:file_id]',
        					'constraints' => array(
        						'file_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),       			
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/view/[:file_id]',
        					'constraints' => array(
        						'file_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),      			
        			'download-revision' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/download-revision/[:file_id]',
        					'constraints' => array(
        						'file_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'downloadRevision'
        					)
        				)
        			),    			
        			'preview-revision' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/preview-revision/[:file_id]',
        					'constraints' => array(
        						'file_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'previewRevision'
        					)
        				)
        			),    			
        			'view-review' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/view-review/[:file_id]',
        					'constraints' => array(
        						'file_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'viewReview'
        					)
        				)
        			),
        		)
        	), //end Files Routes

        	'ips' => array( //Ips Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/ips',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Ips',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/remove/[:ip_id]',
        					'constraints' => array(
        						'file_id' => '[0-9]+'
        					),
        					'defaults' => array( 
        						'controller' => 'PM\Controller\Ips',
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/add',
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/[:ip_id]',
        					'constraints' => array(
        						'ip_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),      			
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/view/[:ip_id]',
        					'constraints' => array(
        						'ip_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),    			
        			'enable' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/enable',
        					'defaults' => array(
        						'action' => 'enable'
        					)
        				)
        			),
        		)
        	), //end IP Routes



        	'json' => array( //Json Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/json',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Json',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'chain-projects' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/chain-projects/[:_value]',
        					'constraints' => array(
        						'_value' => '[0-9]+'
        					),
        					'defaults' => array( 
        						'action' => 'chainProjects'
        					)
        				)
        			),
        			'chain-tasks' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/chain-tasks/[:_value]',
        					'defaults' => array(
        						'action' => 'chain-tasks'
        					)
        				)
        			),
        		)
        	), //end Json Routes




        	'notes' => array( //Notes Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/notes',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Notes',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'all' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/[:type][/:id]',
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
        					'route' => '/remove/[:note_id]',
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
        					'route' => '/add[/:type][/:id]',
        					'constraints' => array(
        						'id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/[:note_id]',
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
        					'route' => '/view/[:note_id]',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        		)
        	), //end Notes Routes


        	'options' => array( //Options Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/options',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Options',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/remove/[:option_id]',
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
        					'route' => '/add',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/[:option_id]',
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
        					'route' => '/view/[:option_id]',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        		)
        	), //end Option Routes





        	'projects' => array( //Project Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/projects',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Projects',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'all' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/[:company_id]',
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
        					'route' => '/view/[:project_id]',
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
        			)
        		)
        	), //end Project Routes



        	'roles' => array( //Roles Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/roles',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Roles',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/remove/[:role_id]',
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
        					'route' => '/add',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/[:role_id]',
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
        					'route' => '/view/[:role_id]',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),
        		)
        	), //end Roles Routes

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
        					'route' => '/password',
        					'defaults' => array(
        						'action' => 'password'
        					)
        				)
        			),
        			'prefs' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/prefs',
        					'defaults' => array(
        						'action' => 'prefs'
        					)
        				)
        			),
        			'notifications' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/prefs',
        					'defaults' => array(
        						'action' => 'prefs'
        					)
        				)
        			)
        		)
        	), //end Settings Routes


        	'tasks' => array( //Tasks Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/tasks',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Tasks',
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
        					'route' => '/remove/[:task_id]',
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
        					'route' => '/add/[:project_id]',
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
        					'route' => '/edit/[:task_id]',
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
        					'route' => '/view/[:task_id]',
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
        					'route' => '/view/[:task_id]/[:progress]',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'updateProgress'
        					)
        				)
        			),
        		)
        	), //end Tasks Routes


        	'timers' => array( //Timers Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/timers',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Timers',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'remove' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/remove/[:timer_id]',
        					'constraints' => array(
        						'timer_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/view/:type/:id',
        					'constraints' => array(
        						'id' => '[0-9]+',
        						'type' => '[a-zA-Z][a-zA-Z0-9_-]+'
        					),
        					'defaults' => array(
        						'action' => 'view'
        					)
        				)
        			),  
        			'start' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/add/[:project_id]',
        					'constraints' => array(
        						'project_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),        			
        			'stop' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit/[:task_id]',
        					'constraints' => array(
        						'note_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			),
        		)
        	), //end Timers Routes



        	'times' => array( //Times Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/times',
        			'defaults' => array(
        				'controller' => 'PM\Controller\Times',
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
        					'route' => '/remove/[:user_id]',
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
        					'route' => '/add',
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			)
        		)
        	), //End Times Routes

        	'users' => array( //User Routes
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
        					'route' => '/remove/[:user_id]',
        					'constraints' => array(
        						'user_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'remove'
        					)
        				)
        			),
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/add',
        					'defaults' => array(
        						'action' => 'add'
        					)
        				)
        			),
        			'edit' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/edit[/:user_id]',
        					'constraints' => array(
        						'user_id' => '[0-9]+'
        					),
        					'defaults' => array(
        						'action' => 'edit'
        					)
        				)
        			)
        		)
        	), //End User Routes
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
