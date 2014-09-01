<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/AbstractPmController.php
 */

namespace PM\Controller;

use Application\Controller\AbstractController;
use PM\Traits\Controller AS PMController;

/**
 * PM - AbstractPmController Controller
 *
 * @package 	MojiTrac
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/AbstractPmController.php
 */
abstract class AbstractPmController extends AbstractController
{	
	use PMController;
	/**
	 * Session
	 * @var object
	 */
	protected $session;
	
	/**
	 * Permission Object
	 * @var object
	 */
	protected $perm;
	
	/**
	 * Settings array
	 * @var array
	 */
	protected $settings;
	
	/**
	 * Preferences array
	 * @var array
	 */
	protected $prefs;
		
	/**
	 * (non-PHPdoc)
	 * @see \Application\Controller\AbstractController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if( empty($this->identity) )
		{
			return $this->redirect()->toRoute('login');
		}	
		
		$settings = $this->getServiceLocator()->get('Application\Model\Settings'); 
		$this->settings = $settings->getSettings();	
		
		$this->getServiceLocator()->get('Timezone');
		
		$this->_initPrefs();
		$this->perm = $this->getServiceLocator()->get('Application\Model\Permissions');
		
		$translator = $e->getApplication()->getServiceManager()->get('translator');
		$translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))->setFallbackLocale('en_US');		
				
		$this->layout()->setVariable('messages',  $this->flashMessenger()->getMessages());
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('identity', $this->identity);
		$this->_initIpBlocker();
		$this->_initEvents();
		
		return parent::onDispatch( $e );
	}
	
	/**
	 * Provides oversight on permission dependant requsts
	 * @param string $permission
	 * @param string $url
	 */
	public function check_permission($permission, $url = FALSE)
	{
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if( empty($this->identity) )
		{
			return $this->redirect()->toRoute('login');
		}
					
		if(!$this->perm->check($this->identity, $permission))
		{
			if(!$url)
			{
				return $this->redirect()->toRoute('pm');
			}
		}
	}
}