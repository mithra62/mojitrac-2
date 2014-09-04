<?php
/**
 * mithra62 - MojiTrac
 *
 * @package		Default
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/config/module.config.php
*/

return array(
    'router' => array(
        'routes' => array(
        	'freshbooks' => array( //Login Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/freshbooks',
        			'constraints' => array(
        				'id' => '[0-9]+'
        			),
        			'defaults' => array(
        				'controller' => 'Freshbooks\Controller\Index',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'process' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/process',
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

        	'forgot-password' => array( //Forgot Password Routes
        		'type' => 'segment',
        		'options' => array(
        			'route' => '/forgot-password',
        			'constraints' => array(
        				'id' => '[0-9]+'
        			),
        			'defaults' => array(
        				'controller' => 'Application\Controller\ForgotPassword',
        				'action' => 'index'
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'reset' => array(
        				'type' => 'segment',
        				'options' => array(
        					'route' => '/reset/:hash',
        					'defaults' => array(
        						'action' => 'reset'
        					)
        				)
        			),
        		)
        	), //end Bookmarks Routes
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Freshbooks\Controller\Index' => 'Freshbooks\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
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
                'text_domain' => 'freshbooks',
            ),
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
