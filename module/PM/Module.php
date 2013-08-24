<?php
namespace PM;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Db\Sql\Sql;

use PM\Model\Projects;

class Module
{
	/**
	 * Sets the layout to use the PM special one
	 * @param ModuleManager $moduleManager
	 */
	public function init(ModuleManager $moduleManager)
	{
		$sharedEvents = $moduleManager->getEventManager()->getSharedManager();
		$sharedEvents->attach(__NAMESPACE__, 'dispatch', function($e) {
			// This event will only be fired when an ActionController under the MyModule namespace is dispatched.
			$controller = $e->getTarget();
			$controller->layout('layout/pm');
		}, 100);
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
				'PM\Model\Projects' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Projects($adapter, $db);
				}
			),
    	);
    }    
}
