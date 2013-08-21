<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/CalendarController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Calendar Controller
*
* Routes the Calendar requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/CalendarController.php
*/
class Pm_CalendarController extends PM_Abstract
{
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        $this->view->headTitle('Calendar', 'PREPEND');
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->active_nav = 'calendar';
        $this->view->sub_menu_options = PM_Model_Options_Companies::types();
        $this->view->uri = $this->_request->getPathInfo();
		$this->view->active_sub = 'None';
		$this->view->title = FALSE;          
	}
    
    public function indexAction()
    {
    	$cal = new PM_Model_Calendar(new PM_Model_DbTable_Projects, new PM_Model_DbTable_Tasks);
    	$date = $this->_request->getParam('date', FALSE);
    	if(!$date)
    	{
	    	$month = date('m');
	    	$year = date('Y');
    	} 
    	else 
    	{
    		$parts = explode(' ',urldecode($date));
    		$month = $cal->translateMonth($parts['0']);
    		$year = $parts['1'];
    	}
    	
    	$this->view->month = $month;
    	$this->view->year = $year;
    	
    	if($this->perm->check($this->identity, 'manage_projects'))
    	{
    		$this->view->calendar_data = $cal->getAllCalendarItems($month, $year);
    	}
    	else
    	{
    		$this->view->calendar_data = $cal->getUserProjectItems($month, $year, $this->identity);
    	} 	
    }
    
    public function viewAction()
    {
    	//$this->view->layout()->disableLayout();
    }
    
    public function viewDayAction()
    {
        $date = $this->_request->getParam('date', date('Y-m-d'));
    	$this->view->date = $date;
    	if($this->perm->check($this->identity, 'manage_projects'))
    	{
    		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
    		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);    		
    		$this->view->project_data = $project->getProjectsByStartDate($date);
    		$this->view->task_data = $task->getTasksByStartDate($date);
    	}
    	else
    	{
    		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
    		$this->view->project_data = $user->getAssignedProjects($this->identity, $date);
    		$this->view->task_data = $user->getAssignedTaskByDate($this->identity, $date);
    	}    	
    }
}