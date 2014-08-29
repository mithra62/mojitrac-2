<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/ActivityController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Activity Logs Controller
*
* Routes the Activity Logs requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/ActivityController.php
*/
class ActivityController extends AbstractPmController
{
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        $this->view->headTitle('Activity Logs', 'PREPEND');
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'companies';
        $this->view->active_nav = 'companies';
        $this->view->uri = $this->_request->getPathInfo();
		$this->view->active_sub = 'None';
		$this->view->title = FALSE;          
	}
    
    public function viewAction()
    {
    	$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
    		if($this->view_mode != 'ajax')
    		{
				$this->_helper->redirector('index','index');
				exit; 
    		}
		}    	
    	$activity = new PM_Model_ActivityLog;
    	$log_entry = $activity->getActivityById($id);
    	
    	if(!$log_entry)
    	{
    		if($this->view_mode != 'ajax')
    		{
				$this->_helper->redirector('index','index');
				exit; 
    		}   		
    	}
    	
    	$this->view->log_entry = $log_entry;
    	
    }
    
    public function indexAction()
    {

    }
}