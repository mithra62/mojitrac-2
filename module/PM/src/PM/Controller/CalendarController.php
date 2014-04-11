<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/CalendarController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Bookmarks Controller
*
* Routes the Calendar requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/CalendarController.php
*/
class CalendarController extends AbstractPmController
{
	/**
	 * Class preDispatch
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );  
		return $e;       
	}
    
    public function indexAction()
    {
    	$cal = $this->getServiceLocator()->get('PM\Model\Calendar');
    	$month = $this->params()->fromRoute('month');
    	$year = $this->params()->fromRoute('year');
    	
    	$view['month'] = $month;
    	$view['year'] = $year;
    	
    	if($this->perm->check($this->identity, 'manage_projects'))
    	{
    		$view['calendar_data'] = $cal->getAllCalendarItems($month, $year);
    	}
    	else
    	{
    		$view['calendar_data'] = $cal->getUserProjectItems($month, $year, $this->identity);
    	} 
    	
    	return $view;
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