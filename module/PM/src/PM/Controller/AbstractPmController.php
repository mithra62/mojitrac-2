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

use Application\Adapter\AuthAdapter;
use Zend\XmlRpc\Value\ArrayValue;
use Zend\Db\Sql\Sql;

use Application\Controller\AbstractController;
use Application\Model\Settings;

 /**
 * Default - AbstractPmController Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/AbstractPmController.php
 */
abstract class AbstractPmController extends AbstractController
{	
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
		
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if( empty($this->identity) )
		{
			return $this->redirect()->toRoute('login');
		}
		
		$settings = new Settings($this->getAdapter(), new Sql($this->getAdapter()));
		$this->settings = $settings->getSettings();	
		
		return parent::onDispatch( $e );
	}	
}