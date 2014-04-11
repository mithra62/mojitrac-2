<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Companies.php
 */

namespace PM\Model;

use Application\Model\AbstractModel;

 /**
 * PM - The Calendar Object
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Calendar.php
 */
class Calendar extends AbstractModel
{
	/**
	 * The Calendar Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
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
		$sql = $sql->where(new \Zend\Db\Sql\Expression('date_format(p.start_date,"%Y-%m") = '."'$year-$month'"));	
		
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
	    $date = new \Zend\Db\Sql\Expression('date_format(start_date,"%Y-%m")');
		$sql = $this->db->select()->from('projects')->columns(array('start_date', 'name', 'id', 'status'))
					   ->where($date->getExpression()." = '$year-$month'");
		
		return $this->getRows($sql);
	}
	
	/**
	 * Returns all the tasks for the given date range
	 * @param unknown_type $month
	 * @param unknown_type $year
	 * @param unknown_type $assigned
	 */
	public function getStartedTasksByDate($month, $year, $assigned = FALSE)
	{
		$date = new \Zend\Db\Sql\Expression('date_format(start_date,"%Y-%m")');
		$sql = $this->db->select()->from('tasks')
		->columns(array('start_date' => new \Zend\Db\Sql\Expression('date_format(start_date,"%Y-%m-%d")'), 'name', 'id', 'status'))
					   ->where($date->getExpression()." = '$year-$month'");

		if($assigned)
		{
			$sql = $sql->where(array('assigned_to' => $assigned));
		}
		
		return $this->getRows($sql);
	}

	public function getCompletedTasksByDate($month = FALSE, $year = FALSE, $assigned = FALSE)
	{
		$date = new \Zend\Db\Sql\Expression('date_format(start_date,"%Y-%m")');
		$sql = $this->db->select()->from('tasks')
		->columns(array('start_date' => new \Zend\Db\Sql\Expression('date_format(start_date,"%Y-%m-%d")'), 'name', 'id', 'status'))
		                 ->where($date->getExpression()." = '$year-$month'");
		
		if($assigned)
		{
			$sql = $sql->where(array('assigned_to' => $assigned));
		}
						   
		return $this->getRows($sql);
	}
}