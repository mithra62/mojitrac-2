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
	'sub_primary_url' => '.moji2.com',    
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
        			'process' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/signup',
        					'defaults' => array(
        						'action' => 'process'
        					)
        				)
        			),
        			'logout' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/logout',
        					'defaults' => array(
        						'action' => 'logout'
        					)
        				)
        			),
        		)
        	), //end Login Routes
        )
    ),
		
    'controllers' => array(
        'invokables' => array(
            'HostManager\Controller\Accounts' => 'HostManager\Controller\AccountsController',
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