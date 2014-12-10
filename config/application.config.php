<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./config/application.config.php
*/

return array(
    'modules' => array(
        'Base',
        'Application',
        'PM',
        'Api',
        'ZF\ApiProblem',
        'Freshbooks',
    	'HostManager', //keep this as last at all times if being hosted
        'ZendDeveloperTools',
    	'ZfSimpleMigrations'
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php'
        )
    )
);
