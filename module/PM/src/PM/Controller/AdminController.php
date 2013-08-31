<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/AdminController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Admin Controller
*
* Routes the Administration Panel requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/AdminController.php
*/
class AdminController extends AbstractPmController
{
	/**
	 * Class preDispatch
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        parent::check_permission('admin_access');
        //$this->view->headTitle('Administration', 'PREPEND');  
        //$this->view->uri = $this->_request->getPathInfo();
		$this->layout()->setVariable('active_nav', 'admin');
		$this->layout()->setVariable('sub_menu', 'admin');

		return $e;
	}
    
    public function indexAction()
    {

    }
    
    public function clearCacheAction()
    {
    	$setting = new Model_Settings;
		if($setting->cache->clean(Zend_Cache::CLEANING_MODE_ALL))
		{
	    	$this->_flashMessenger->addMessage('Cache Cleaned!!');
			$this->_helper->redirector('index','admin');  
	    	exit;
		}
    }
    
    public function systemResetAction()
    {
        $this->view->layout_style = 'right';
        
        $this->view->phrase = 'Yes, I want to reset MojiTrac to the default system.';
    	$setting = $this->getServiceLocator()->get('Application\Model\Settings');
		$form = $setting->getResetForm(array(
            'action' => '/pm/admin/system-reset',
            'method' => 'post',
        ), $this->view->phrase);
        
		if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				$admin = new PM_Model_Admin;
				if($admin->systemReset())
				{
			    	$this->_flashMessenger->addMessage('System Reset!');
					return $this->redirect()->toRoute('admin');					
				}
			}   

		}
        
        $this->view->form = $form;
    } 

    /**
     * Handles the system global settings.
     */
    public function settingsAction()
    {
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'admin');
        $this->layout()->setVariable('active_sub', 'global');
        $this->layout()->setVariable('layout_style','right');
            	
    	$setting = $this->getServiceLocator()->get('Application\Model\Settings');
		$form = $this->getServiceLocator()->get('Application\Form\SettingsForm');
        
        $settings = $setting->getSettings();
        $form->setData($settings);
        $view['settings'] = $this->settings;
        
       	if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
			if($setting->updateSettings($formData))
			{
		    	$this->flashMessenger()->addMessage('Settings updated!');
				return $this->redirect()->toRoute('admin/settings');	
			}
		}
		
		$view['form'] = $form;
		return $view;
    }
    
    public function optionsAction()
    {
    	
    }
}