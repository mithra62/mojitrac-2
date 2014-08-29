<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Controllers/AbstractController.php
 */

namespace Application\Controller;

use Base\Controller\BaseController;

 /**
 * Default - AbstractController Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/src/Application/Controllers/AbstractController.php
 */
abstract class AbstractController extends BaseController
{

	/**
	 * (non-PHPdoc)
	 * @see \Base\Controller\BaseController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		return parent::onDispatch( $e );
	}
	
	public function logoutAction()
	{
		$login = $this->getServiceLocator()->get('Application\Model\Login');
		$login->logout($this->getSessionStorage(), $this->getAuthService());

		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		$this->flashmessenger()->addMessage($translate('youve_been_logged_out', 'app'));
		return $this->redirect()->toRoute('login');
	}	
}