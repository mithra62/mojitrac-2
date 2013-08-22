<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    // Whether or not to enable a configuration cache.
    // If enabled, the merged configuration will be cached and used in
    // subsequent requests.
    'config_cache_enabled' => false,
    // The key used to create the configuration cache file name.
    'config_cache_key' => 'module_config_cache',
    // The path in which to cache merged configuration.
    'cache_dir' =>  './data/cache',
	'db' => array(
			'driver'         => 'Pdo',
			'dsn'            => 'mysql:dbname=dev_moji_install;host=localhost',
			'driver_options' => array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
			),
	),
	'service_manager' => array(
			'factories' => array(
					'Zend\Db\Adapter\Adapter'
					=> 'Zend\Db\Adapter\AdapterServiceFactory',
			),
			'invokables' => array(
					'Zend\Authentication\AuthenticationService' => 'Zend\Authentication\AuthenticationService',
			),			
	),
);
