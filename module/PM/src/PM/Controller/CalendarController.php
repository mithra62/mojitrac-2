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
    	$month = $this->params()->fromRoute('month', date('m'));
    	$year = $this->params()->fromRoute('year', date('Y'));
    	
    	$view['month'] = $month;
    	$view['year'] = $year;
    	if($this->perm->check($this->identity, 'manage_projects'))
    	{
    		$view['calendar_data'] = $cal->getAllItems($month, $year);
    	}
    	else
    	{
    		$view['calendar_data'] = $cal->getUserItems($month, $year, $this->identity);
    	} 
    	
    	return $view;
    }
    
    public function viewDayAction()
    {
    	$month = $this->params()->fromRoute('month');
    	$year = $this->params()->fromRoute('year');
    	$day = $this->params()->fromRoute('day');
    	
    	$view['month'] = $month;
    	$view['day'] = $day;
    	$view['year'] = $year;
    	if($this->perm->check($this->identity, 'manage_projects'))
    	{
    		$project = $this->getServiceLocator()->get('PM\Model\Projects');
    		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    		$view['project_data'] = $project->getProjectsByStartDate($year, $month, $day);
    		$view['task_data'] = $task->getTasksByStartDate($year, $month, $day);
    	}
    	else
    	{
    		$user = $this->getServiceLocator()->get('PM\Model\Users');
    		$view['project_data'] = $user->getAssignedProjects($this->identity, $year, $month, $day);
    		$view['task_data']' = $user->getAssignedTaskByDate($this->identity, $year, $month, $day);
    	}   

    	return $view;
    }
}