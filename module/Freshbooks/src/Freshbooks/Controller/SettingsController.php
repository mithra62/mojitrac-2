<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/src/Freshbooks/Controller/SettingsController.php
 */

namespace Freshbooks\Controller;

use PM\Controller\AbstractPmController;

/**
 * Freshbooks - Settings Controller
 *
 * Routes the Settings requests
 *
 * @package 	Freshbooks\Settings
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Freshbooks/src/Freshbooks/Controller/SettingsController.php
 */
class SettingsController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(\Zend\Mvc\MvcEvent $e)
	{
		$e = parent::onDispatch($e);
		$this->layout()->setVariable('active_nav', 'admin');	
		return $e;
	}
		
    public function indexAction()
    {
       
		$this->layout()->setVariable('layout_style', 'left');
		
		return array();
    }
    
    public function linkAccountAction()
    {
    	print_r($this->settings);
    	exit;
		$form = $this->getServiceLocator()->get('Freshbooks\Form\CredentialsForm');
		$form->setData(
			array(
				'status' => $this->settings['default_project_status'],
				'type' => $this->settings['default_project_type'],
				'priority' => $this->settings['default_project_priority'],
			)
		);
    	echo 'fdsfdsafdsaa';
    	exit;
    	exit;
    }
}
