<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/HostManager/config/module.config.php
*/

return array(
	'sub_primary_url' => '.mojitrac.com', 
	'master_host_account' => '1',
	'router' => array(
        'routes' => array(
        	'hosted-accounts' => array( //Account Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/account',
        			'defaults' => array(
        				'controller' => 'HostManager\Controller\Accounts',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'signup' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/signup[/:status]',
        					'defaults' => array(
        						'action' => 'signup'
        					)
        				)
        			),
        		)
        	), //end Account Routes
        	'api-hosted-accounts' => array( //Hosted Accounts Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/api/accounts[/:id]',
        			'defaults' => array(
        				'controller' => 'HostManager\Controller\AccountsApi',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array( 
        		)
        	), //end Hosted Accounts 

        	'users' => array( //User Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/pm/users',
        			'defaults' => array(
        				'controller' => 'HostManager\Controller\Users',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'view' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/[:user_id]',
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
        					'route' => '/remove/:user_id',
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
        			'invite' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/invite',
        					'defaults' => array(
        						'action' => 'invite'
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
        )
    ),
		
    'controllers' => array(
        'invokables' => array(
            'HostManager\Controller\Accounts' => 'HostManager\Controller\AccountsController',
            'HostManager\Controller\AccountsApi' => 'HostManager\Controller\AccountsApiController',
            'HostManager\Controller\Users' => 'HostManager\Controller\UsersController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'template_map' => array(
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),    
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
                'text_domain' => 'hm',
            ),
        ),
    ),
);