<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/modules/pm/models/Calendar.php
 */

 /**
 * PM - The Calendar Object
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/modules/pm/models/Calendar.php
 */
class PM_Model_Calendar extends Model_Abstract
{
	/**
	 * The PM Calendar Object
	 * @param PM_Model_DbTable_Projects_Teams $project
	 * @param PM_Model_DbTable_Tasks $task
	 */
	public function __construct(PM_Model_DbTable_Projects $project = null, PM_Model_DbTable_Tasks $task = null)
	{
		$this->project = $project;
		$this->task = $task;
	}
	
	/**
	 * Returns all projects and tasks for the given date 
	 * @param int $month
	 * @param int $year
	 */
	public function getAllCalendarItems($month, $year)
	{
    	$started_projects = $this->getStartedProjectsByDate($month, $year);
    	$started_tasks = $this->getStartedTasksByDate($month, $year);
    	$complete_tasks = $this->getCompletedTasksByDate($month, $year);
    	
    	$stuff = FALSE;
    	if(is_array($started_projects))
    	{
    		$stuff = $this->_translateItems($started_projects, 'start_date', 'projects');
    	}
    	
	    if(is_array($started_tasks))
    	{
    		$stuff = $this->_translateItems($started_tasks, 'start_date', 'tasks', $stuff);
    	} 
    	
	    if(is_array($complete_tasks))
    	{
    		$stuff = $this->_translateItems($complete_tasks, 'end_date', 'tasks', $stuff);
    	} 
    	
    	return $stuff;
	}
	
	/**
	 * Returns all the projects and tasks belonging to a given user for the date range
	 * @param int $month
	 * @param int $year
	 * @param int $identity
	 */
	public function getUserProjectItems($month, $year, $identity)
	{
		$proj_team = $this->projects;
		$sql = $proj_team->select()->setIntegrityCheck(false)->from(array('pt' =>$proj_team->getTableName()), array('project_id'))->where('pt.user_id = ?', $identity);
		$sql = $sql->join(array('p' => 'projects'), 'p.id = pt.project_id');
		$sql = $sql->where(new Zend_Db_Expr('date_format(p.start_date,"%Y-%m") = '."'$year-$month'"));	
		
		$arr = $this->_translateItems($proj_team->getProjectTeamMembers($sql), 'start_date', 'projects');
		$arr = $this->_translateItems($this->getStartedTasksByDate($month, $year, $identity), 'start_date', 'tasks', $arr);
		$arr = $this->_translateItems($this->getCompletedTasksByDate($month, $year, $identity), 'end_date', 'tasks', $arr);
		
		return $arr;
	}
	
	/**
	 * Takes an array of stuff and returns a new one formatted for use in the calendar object
	 * @param array $arr
	 * @param string $master_key
	 * @param string $view
	 * @param mixed $_arr
	 */
	private function _translateItems($arr, $master_key, $view = 'projects', $_arr = array())
	{
		if(!is_array($_arr))
		{
			$_arr = array();
		}
		
		foreach($arr AS $key)
		{
			$_arr[$key[$master_key]][] = array(
											'string' => $key['name'],
											'rel' => '',
											'type' => $view,
											'status' => $key['status'],
											'href' => '/pm/'.$view.'/view/id/'.$key['id']
			);
		}
		
		return $_arr;		
	}
	
	/**
	 * Returns all the projects that were started by the date range
	 * @param int $month
	 * @param int $year
	 */
	public function getStartedProjectsByDate($month, $year)
	{
		$project = $this->project;
		$sql = $project->select()
					   ->from($project->getTableName(), array('start_date', 'name', 'id', 'status'))
					   ->where(new Zend_Db_Expr('date_format(start_date,"%Y-%m")').' = ?', "$year-$month");
		
		return $project->getProjects($sql);
	}
	
	/**
	 * Returns all the tasks for the given date range
	 * @param unknown_type $month
	 * @param unknown_type $year
	 * @param unknown_type $assigned
	 */
	public function getStartedTasksByDate($month, $year, $assigned = FALSE)
	{
		$task = $this->task;
		$sql = $task->select()
					   ->from($task->getTableName(), array('date_format(start_date,"%Y-%m-%d") AS start_date', 'name', 'id', 'status'))
					   ->where(new Zend_Db_Expr('date_format(start_date,"%Y-%m")').' = ?', "$year-$month");

		if($assigned)
		{
			$sql = $sql->where('assigned_to = ?', $assigned);
		}
		
		return $task->getTasks($sql);
	}

	public function getCompletedTasksByDate($month = FALSE, $year = FALSE, $assigned = FALSE)
	{
		$task = $this->task;
		$sql = $task->select()
					   ->from($task->getTableName(), array('date_format(end_date,"%Y-%m-%d") AS end_date', 'name', 'id', 'status'))
					   ->where(new Zend_Db_Expr('date_format(end_date,"%Y-%m")').' = ?', "$year-$month");
		
		if($assigned)
		{
			$sql = $sql->where('assigned_to = ?', $assigned);
		}					   
		return $task->getTasks($sql);
	}	

	public function translateMonth($month_name)
	{
		$months = array();
		$months['January'] = '01';
		$months['Febuary'] = $months['February'] = '02';
		$months['March'] = '03';
		$months['April'] = '04';
		$months['May'] = '05';
		$months['June'] = '06';
		$months['July'] = '07';
		$months['August'] = '08';
		$months['September'] = '09';
		$months['October'] = '10';
		$months['November'] = '11';
		$months['December'] = '12';
		
		if(array_key_exists($month_name, $months))
		{
			return $months[$month_name];
		}
	}	
}