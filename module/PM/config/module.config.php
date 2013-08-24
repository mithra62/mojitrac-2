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
        	
        	// Literal route named "blog", with child routes
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
        					'route' => '/pm/projects/view/[:id]',
        					'constraints' => array(
        						'slug' => '[a-zA-Z0-9_-]+'
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
        	)    		
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'PM\Controller\Activity' => 'PM\Controller\ActivityController',
            'PM\Controller\Admin' => 'PM\Controller\AdminController',
            'PM\Controller\Index' => 'PM\Controller\IndexController',
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
	    	'StaticUrl' => 'Application\View\Helper\StaticUrl',
	    	'GlobalAlerts' => 'PM\View\Helper\GlobalAlerts',
	    	'CheckPermission' => 'PM\View\Helper\CheckPermission',
	    	'DashboardTimeline' => 'PM\View\Helper\DashboardTimeline',
	    	'ProjectStatus' => 'PM\View\Helper\ProjectStatus',
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
