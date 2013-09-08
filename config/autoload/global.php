<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./config/autoload/global.php
 */

return array(
    'config_cache_enabled' => false,
    'config_cache_key' => 'module_config_cache',
    'cache_dir' =>  './data/cache',
	'db' => array(
		'driver'  => 'Pdo',
		'dsn' => 'mysql:dbname=dev_moji_install;host=localhost',
		'driver_options' => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		),
	),
    //see http://framework.zend.com/manual/2.1/en/modules/zend.mail.smtp.options.html for complete options
    'email' => array(
	   'type' => 'php', //choose between `php` and `smtp`
       'smtp_options' => array( //if `smtp` chosen above, this must be completed and accurate 
       
           'name' => 'localhost.localdomain',
           'host' => '127.0.0.1',
           'connection_class' => 'login',
           'connection_config' => array(
           	    'username' => 'user',
           		'password' => 'pass',
           )    	   
        ) 
    ),
	'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
		'invokables' => array(
            'Zend\Authentication\AuthenticationService' => 'Zend\Authentication\AuthenticationService',
		),			
	),
);
