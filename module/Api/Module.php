<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Api/Module.php
 */
namespace Api;

use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\View\Model\JsonModel;

use Api\Model\Projects;
use Api\Model\Tasks;
use Api\Model\Users;
use Api\Model\Companies;
use Api\Model\Options;
use Api\Model\Roles;

/**
 * Api - Module Object
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/Module.php
 */
class Module implements Feature\BootstrapListenerInterface
{
	public function onBootstrap(EventInterface $e)
	{	
		//we have to work some magic to only use the Json ViewStrategy on the API module
		$app = $e->getApplication();
		$em  = $app->getEventManager()->getSharedManager();
		$sm  = $app->getServiceManager();
		$em->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function($e) use ($sm) {
			$strategy = $sm->get('ViewJsonStrategy');
			$view     = $sm->get('ViewManager')->getView();
			$strategy->attach($view->getEventManager());
		});	
		
		$em->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH_ERROR, function($e) use ($sm) {
			return $this->getJsonModelError($e);
		});		
	}   
	
	public function getServiceConfig()
    {
    	return array(
			'factories' => array(
					
				//models
				'Api\Model\Projects' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Projects($adapter, $db);
				}, 
				'Api\Model\Tasks' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Tasks($adapter, $db);
				},	 
				'Api\Model\Users' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					$role = $sm->get('Api\Model\Roles');
					return new Users($adapter, $db, $role);
				},	 
				'Api\Model\Companies' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Companies($adapter, $db);
				},	 
				'Api\Model\Options' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Options($adapter, $db);
				},	 
				'Api\Model\Roles' => function($sm) {
					$adapter = $sm->get('Zend\Db\Adapter\Adapter');
					$db = $sm->get('SqlObject');
					return new Roles($adapter, $db);
				},									
			),
    	);
    } 
	
	
	
	public function onDispatchError($e)
	{
		return $this->getJsonModelError($e);
	}
	
	public function onRenderError($e)
	{
		return $this->getJsonModelError($e);
	}
	
	public function getJsonModelError($e)
	{
		$error = $e->getError();
		if (!$error) {
			return;
		}
	
		$response = $e->getResponse();
		$exception = $e->getParam('exception');
		$exceptionJson = array();
		if ($exception) {
			$exceptionJson = array(
					'class' => get_class($exception),
					'file' => $exception->getFile(),
					'line' => $exception->getLine(),
					'message' => $exception->getMessage(),
					'stacktrace' => $exception->getTraceAsString()
			);
		}
	
		$errorJson = array(
				'message' => 'An error occurred during execution; please try again later.',
				'error' => $error,
				'exception' => $exceptionJson,
		);
		if ($error == 'error-router-no-match') {
			$errorJson['message'] = 'Resource not found.';
		}
	
		$model = new JsonModel(array('errors' => array($errorJson)));
	
		$e->setResult($model);
	
		return $model;
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
}
