<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @subpackage	HostManager
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/Module.php
 */

namespace HostManager;

/**
 * HostManager - Module Object
 *
 * @package 	mithra62:Mojitrac
 * @subpackage	HostManager
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/Module.php
 */
class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    

    public function getServiceConfig()
    {
    	return array(
		'factories' => array(
					'HostManager\Model\Sql' => function($sm) {
					//$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					//$db = $sm->get('SqlObject');
					return '';//new Projects($adapter, $db);
				},
			)
		);
    }
}
