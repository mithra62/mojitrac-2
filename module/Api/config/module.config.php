<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Api/config/module.config.php
*/

return array(
		
	'router' => array(
		'routes' => array(
			'api' => array(
				'type'    => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/api',
					'defaults' => array(
						'controller' => 'Api\Controller\Index',
						'action'     => 'index',
					),
				),
        		'may_terminate' => true,
        		'child_routes' => array(    			
					'chain-projects' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/chain-projects',
							'defaults' => array(
								'action' => 'chainProjects'
							)
						)
					),   			
					'chain-tasks' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/chain-tasks[/:project_id]',
							'constraints' => array(
								'project_id' => '[0-9]*'
							),
							'defaults' => array(
								'action' => 'chainTasks'
							)
						)
					)
        		)
			),
			'api/projects/chain-projects' => array(
				'type'    => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/api/projects/chain-projects',
					'defaults' => array(
        				'controller' => 'Api\Controller\Projects',
						'action' => 'chainTasksAction'
					),
				),
			),
			'api-projects' => array( //Project Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/api/projects[/:id]',
        			'defaults' => array(
        				'controller' => 'Api\Controller\Projects'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(   
        		),
        	),   
        	//end Project Routes
        	'api-tasks' => array( //Tasks Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/api/tasks[/:id]',
        			'defaults' => array(
        				'controller' => 'Api\Controller\Tasks',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array( 
        		)
        	), //end Tasks Routes
        	'api-users' => array( //Users Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/api/users[/:id]',
        			'defaults' => array(
        				'controller' => 'Api\Controller\Tasks',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array( 
        		)
        	), //end Users Routes
        			
				
				
		),
	),      
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
                'text_domain' => 'api',
            ),
        ),
    ),
    'controllers' => array(
		'invokables' => array(
            'Api\Controller\Index' => 'Api\Controller\IndexController',
            'Api\Controller\Projects' => 'Api\Controller\ProjectsController',
            'Api\Controller\Tasks' => 'Api\Controller\TasksController',
		),
	),
);