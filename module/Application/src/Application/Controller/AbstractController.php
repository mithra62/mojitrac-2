<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
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

	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		return parent::onDispatch( $e );
	}
	
	public function logoutAction()
	{
		$login = $this->getServiceLocator()->get('Application\Model\Login');
		$login->logout($this->getSessionStorage(), $this->getAuthService());
	
		$this->flashmessenger()->addMessage("You've been logged out");
		return $this->redirect()->toRoute('login');
	}	
}