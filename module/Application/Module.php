<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Application/Module.php
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
use Application\Model\User\UserData;
use Application\Model\Permissions;
use Application\Form\LoginForm;
use Application\Model\Login;
use Application\Form\ForgotPasswordForm;
use Application\Model\ForgotPassword;
use Application\Model\Settings;

/**
 * Default - Module Loader
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/Module.php
 */
class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $app = $e->getParam('application');
        $app->getEventManager()->attach('render', array($this, 'setLayoutTitle'));        
    }
    

    /**
     * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function setLayoutTitle($e)
    {
    	$matches    = $e->getRouteMatch();
    	if(!$matches)
    	{
    		return;
    	}
    	
    	$action     = $matches->getParam('action');
    	$controller = $matches->getParam('controller');
    	$module     = __NAMESPACE__;
    	$siteName   = 'MojiTrac';
    
    	// Getting the view helper manager from the application service manager
    	$viewHelperManager = $e->getApplication()->getServiceManager()->get('viewHelperManager');
    
    	// Getting the headTitle helper from the view helper manager
    	$headTitleHelper   = $viewHelperManager->get('headTitle');
    
    	// Setting a separator string for segments
    	$headTitleHelper->setSeparator(' - ');
    
    	// Setting the action, controller, module and site name as title segments
    	$headTitleHelper->append($action);
    	$headTitleHelper->append($controller);
    	$headTitleHelper->append($module);
    	$headTitleHelper->append($siteName);
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
				
				'SqlObject' => function($sm) {
					$db = $sm->get('Zend\Db\Adapter\Adapter');
					return new Sql($db);
				},
				'Application\Form\LoginForm' => function($sm) {
					return new LoginForm('login');
				},				
				'Application\Model\User' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new User($adapter, $db);
				},
				'Application\Model\Permissions' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Permissions($adapter, $db);					
				},
				'Application\Model\Login' => function() {
					return new Login();
				},
				'Application\Model\ForgotPassword' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new ForgotPassword($adapter, $db);					
				},
				'Application\Model\ForgotPasswordForm' => function($sm) {
					return new ForgotPasswordForm('forgot_password');
				},
				'Application\Model\Settings' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Settings($adapter, $db);					
				},
				'Application\Model\User\Data' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new UserData($adapter, $db);					
				}
			),
    	);
    }
}
