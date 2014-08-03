<?php
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
			),
			'api/projects' => array( //Project Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/api/projects',
        			'defaults' => array(
        				'controller' => 'Api\Controller\Projects'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(     			      			
					'chain-projects' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/chain-projects[/:company_id]',
							'defaults' => array(
        						'controller' => 'Api\Controller\Projects',
								'action' => 'chainProjects'
							)
						)
					),
        			'restful' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/:action[/:id]',
							'defaults' => array(
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id' => '[a-zA-Z0-9_-]*'									
							)
						)
					)
        		),
        	),   
        	//end Project Routes
        	'api/tasks' => array( //Tasks Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/api/tasks',
        			'defaults' => array(
        				'controller' => 'Api\Controller\Tasks',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(    			
					'chain-tasks' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/chain-tasks[/:project_id]',
							'constraints' => ['project_id' => '[0-9]*'],
							'defaults' => array(
								'action' => 'chainTasks'
							)
						)
					)
        		)
        	), //end Tasks Routes
        			
				
				
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