<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/controllers/AbstractPmController.php
 */

namespace PM\Controller;

use Application\Controller\AbstractController;

 /**
 * Default - AbstractPmController Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/AbstractPmController.php
 */
abstract class AbstractPmController extends AbstractController
{	
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if( empty($this->identity) )
		{
			return $this->redirect()->toRoute('login');
		}
		
		return parent::onDispatch( $e );
	}	
}