<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/AdminController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Admin Controller
*
* Routes the Admin requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/AdminController.php
*/
class Pm_AdminController extends PM_Abstract
{
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        parent::check_permission('admin_access');
        $this->view->headTitle('Administration', 'PREPEND');  
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'admin';
        $this->view->active_nav = 'admin';
        $this->view->sub_menu_options = PM_Model_Options_Companies::types();
        $this->view->uri = $this->_request->getPathInfo();
		$this->view->active_sub = 'None';
		$this->view->title = FALSE;          
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
    	$setting = new Model_Settings;
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
					$this->_helper->redirector('index','admin');  
					exit;					
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
        $this->view->sub_menu = 'admin';
        $this->view->active_nav = 'admin';
        $this->view->active_sub = 'global';
        $this->view->layout_style = 'right';
            	
    	$setting = new Model_Settings;
		$form = $setting->getSettingForm(array(
            'action' => '/pm/admin/settings',
            'method' => 'post',
        ));
        
        $settings = $setting->getSettings();
        $form->populate($settings);
        $this->view->settings = $this->settings;
        
       	if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				if($setting->updateSettings($formData))
				{
					$setting->cache->remove('pm_settings');
			    	$this->_flashMessenger->addMessage('Settings updated!');
					$this->_helper->redirector('settings','admin');  
					exit;
				}
			}
		}
		
		$this->view->form = $form;
    }
    
    public function optionsAction()
    {
    	
    }
}