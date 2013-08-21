<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;

use Application\Model\Auth\AuthAdapter;
use Application\Model\User;


class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

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
    			//ok. so i started to go the ZF2 Model => Table method for managing tables
    			//but the zf1 code would have required a complete rewrite and would have sucked.
    			//I'm leaving this hear so when we do decide to implement this we'll have an example.
    					'Application\Model\DbTable\UserTable' =>  function($sm) {
    						$tableGateway = $sm->get('UserTableGateway');
    						$table = new UserTable($tableGateway);
    						return $table;
    					},
    					'UserTableGateway' => function ($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype(new User());
    						return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
    					},
    			
    					//setting up the Authentication stuff
    					'Application\Model\Auth\AuthStorage' => function($sm){
    						return new \Application\Model\Auth\AuthStorage('mojitrac');
    					},
    					'AuthService' => function($sm) {
    						$db = $sm->get('Zend\Db\Adapter\Adapter');
    						$user = new User( $db ,  new Sql($db) );
    						$authService = new AuthenticationService();
    						$authService->setAdapter(new AuthAdapter($user));
    						$authService->setStorage($sm->get('Application\Model\Auth\AuthStorage'));
    						return $authService;
    					},
    					//end Auth
    			),
    	);
    }
}
