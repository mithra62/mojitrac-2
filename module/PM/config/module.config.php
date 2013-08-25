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
        			// Segment route for viewing one blog post
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
        			// Literal route for viewing blog RSS feed
        			'add' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => 'add[/:company_id]',
        					'constraints' => ['company_id' => '[0-9]*'],
        					'defaults' => array(
        						'controller' => 'PM\Controller\Projects',
        						'action' => 'add'
        					)
        				)
        			)
        		)
        	),

        	// Literal route named "blog", with child routes
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
        					// Segment route for viewing one blog post
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
        					// Literal route for viewing blog RSS feed
        					'add' => array(
        							'type' => 'segment',
        							'options' => array(
        									'route' => 'add',
        									'defaults' => array(
        											'controller' => 'PM\Controller\Users',
        											'action' => 'add'
        													)
        							)
        					)
        			)
        	),

        	
        	'calendar' => array(
        			'type' => 'segment',
        			'options' => array(
        					'route' => '/pm/calendar',
        					'defaults' => array(
        							'controller' => 'PM\Controller\Calendar',
        							'action' => 'index'
        					),
        			),
        			'may_terminate' => true,
        			'child_routes' => array(
        					// Segment route for viewing one blog post
        					'view' => array(
        							'type' => 'segment',
        							'options' => array(
        									'route' => 'view/[:user_id]',
        									'constraints' => array(
        											'user_id' => '[0-9]+'
        									),
        									'defaults' => array(
        											'action' => 'view'
        									)
        							)
        					),
        					// Literal route for viewing blog RSS feed
        					'add' => array(
        							'type' => 'segment',
        							'options' => array(
        									'route' => 'add',
        									'defaults' => array(
        											'controller' => 'PM\Controller\Users',
        											'action' => 'add'
        									)
        							)
        					)
        			)
        	),     

        	
        	
        	

        	'settings' => array(
        			'type' => 'segment',
        			'options' => array(
        					'route' => '/pm/settings',
        					'defaults' => array(
        							'controller' => 'PM\Controller\Calendar',
        							'action' => 'index'
        					),
        			),
        			'may_terminate' => true,
        			'child_routes' => array(
        					// Segment route for viewing one blog post
        					'view' => array(
        							'type' => 'segment',
        							'options' => array(
        									'route' => 'view/[:user_id]',
        									'constraints' => array(
        											'user_id' => '[0-9]+'
        									),
        									'defaults' => array(
        											'action' => 'view'
        									)
        							)
        					),
        					// Literal route for viewing blog RSS feed
        					'add' => array(
        							'type' => 'segment',
        							'options' => array(
        									'route' => 'add',
        									'defaults' => array(
        											'controller' => 'PM\Controller\Users',
        											'action' => 'add'
        									)
        							)
        					)
        			)
        	),        	
        	
        	
        	
        	
        	
        	
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'PM\Controller\Activity' => 'PM\Controller\ActivityController',
            'PM\Controller\Admin' => 'PM\Controller\AdminController',
            'PM\Controller\Index' => 'PM\Controller\IndexController',
            'PM\Controller\Bookmarks' => 'PM\Controller\BookmarksController',

            'PM\Controller\Projects' => 'PM\Controller\ProjectsController',
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
