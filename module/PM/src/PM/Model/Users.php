<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Users.php
 */

namespace PM\Model;

use Application\Model\Users AS MojiUsers;

 /**
 * PM - User Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Users.php
 */
class Users extends MojiUsers
{
	/**
	 * Returns the number of projects a user $id owns
	 * @param int $id
	 * @return int
	 */
	public function getProjectCount($id)
	{
		$project = new PM_Model_DbTable_Projects;
		$sql = $project->select()
					->from($project->getTableName(), array(new Zend_Db_Expr('COUNT(id) AS count')))
					->where('owner = ?', $id);
		$data = $project->getProject($sql);
		if(is_array($data))
		{
			return $data['count'];
		}
	}
	
	/**
	 * Returns the number of tasks that a user $id owns
	 * @param int $id
	 * @return int
	 */
	public function getTaskCount($id)
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->select()
					->from($task->getTableName(), array(new Zend_Db_Expr('COUNT(id) AS count')))
					->where('owner = ?', $id);
		$data = $task->getTask($sql);
		if(is_array($data))
		{
			return $data['count'];
		}		
	}

	/**
	 * Returns the number of files a given user $id has uploaded
	 * @param int $id
	 * @return int
	 */
	public function getFileCount($id)
	{
		$file = new PM_Model_DbTable_Files;
		$sql = $file->select()
					->from($file->getTableName(), array(new Zend_Db_Expr('COUNT(id) AS count')))
					->where('owner = ?', $id);
		$data = $file->getFile($sql);
		if(is_array($data))
		{
			return $data['count'];
		}		
	}
	
	/**
	 * Returns the project ids a user is assigned to
	 * @param int $id
	 * @return array
	 */
	public function getAssignedProjectIds($id)
	{
		$sql = $this->db->select()->from('project_teams')->columns(array('project_id'))->where(array('user_id' => $id));
		$proj_ids = $this->getRows($sql);
		if($proj_ids)
		{
			$arr = array();
			foreach($proj_ids AS $proj)
			{
				$arr[] = $proj['project_id'];
			}
			return $arr;
		}
	}
	
	/**
	 * Returns the companies for the projects a user is assigned to
	 * @param int $id
	 * @return array
	 */
	public function getAssignedProjectCompanies($id)
	{
		$proj_team = new PM_Model_DbTable_Projects_Teams;
		$sql = $proj_team->select()->setIntegrityCheck(false)->from(array('pt' => $proj_team->getTableName()), array('project_id'))->where('user_id = ?', $id);
		$sql = $sql->join(array('p' => 'projects'), 'p.id = pt.project_id AND p.status != 6', array());
		$sql = $sql->join(array('c' => 'companies'), 'c.id = p.company_id');
		return $proj_team->getProjectTeamMembers($sql);
	}	
	
	/**
	 * Returns the project for the projects $id is attached to
	 * @param int $id
	 * @return array
	 */
	public function getAssignedProjects($id, $start_date = FALSE)
	{
		$sql = $this->db->select()->from(array('pt' => 'project_teams'))->columns(array('project_id'))->where(array('pt.user_id' => $id));
		$sql = $sql->join(array('p' => 'projects'), 'p.id = pt.project_id')->where(array('p.status != 6'));	
		if($start_date)	
		{
			$sql = $sql->where(array('p.start_date' => $start_date));
		}
		$sql = $sql->join(array('c' => 'companies'), 'p.company_id = c.id', array('company_name' => 'name'));
		return $this->getRows($sql);
	}
	
	/**
	 * Returns whether a user has overdue tasks
	 * @param int $id
	 */
	public function userHasOverdueTasks($id)
	{
		$where = array(
			'assigned_to' => $id,
			'progress != 100 AND t.end_date != \'0000-00-00 00:00:00\'',
			'p.status NOT IN(4,5,6)',
			't.status != 4',
			't.end_date <= NOW() ',
		);
		$sql = $this->db->select()->from(array('t'=> 'tasks'))
					->columns( array('total_count' => new \Zend\Db\Sql\Expression("COUNT(t.id)")))
			   		->join(array('p' => 'projects'), 'p.id = t.project_id')
			   		->where($where);
		return $this->getRow($sql);				
	}
	
	/**
	 * Returns all the tasks a user is 
	 * @param int $id
	 * @param int $upcoming
	 * @return array
	 */
	public function getAssignedTasks($id, $upcoming = 30)
	{
		$user_tasks = array();
		$user_tasks['overdue'] = array();
		$user_tasks['today'] = array();
		$user_tasks['tomorrow'] = array();
		$user_tasks['within_week'] = array();
		$user_tasks['upcoming'] = array();
		$today = date('Y-m-d');
		$upcoming_date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$upcoming, date("Y")));
		$tomorrow_date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
		$week_date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+7, date("Y")));
		//$lambLib = new LambLib_Controller_Action_Helper_Utilities;

		$open_tasks = $this->getOpenAssignedTasks($id);
		foreach($open_tasks As $task)
		{
			//$task_date = $lambLib->formatDate($task['end_date'], 'Y-m-d');
			$task_date = $task['end_date'];
			if($task_date == $today)
			{
				$user_tasks['today'][] = $task;
				continue;
			}

			if($task_date == $tomorrow_date)
			{
				$user_tasks['tomorrow'][] = $task;
				continue;
			}
			
			if($task_date <= $week_date && $task_date >= $today)
			{
				$user_tasks['within_week'][] = $task;
				continue;
			}
			
			if($task_date < $today)
			{
				$user_tasks['overdue'][] = $task;
				continue;
			}
							
			if($task_date <= $upcoming_date)
			{			
				$user_tasks['upcoming'][] = $task;
				continue;
			}				
		}
		
		return $user_tasks;		
	}
	
	/**
	 * Returns all the tasks that are starting on $date
	 * @param string $id
	 * @param string $date
	 */
	public function getAssignedTaskByStartDate($id, $date)
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->select()->setIntegrityCheck(false)->from(array('t'=>$task->getTableName()));
		$sql = $sql->where('assigned_to = ?', $id)->where(new Zend_Db_Expr('date_format(t.start_date,"%Y-%m-%d")').' = ?', $date);	
		
		$sql = $sql->joinRight(array('p' => 'projects'), 'p.id = t.project_id', array('name AS project_name'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = t.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
		$sql = $sql->joinLeft(array('u3' => 'users'), 'u3.id = t.assigned_to', array('first_name AS assigned_first_name', 'last_name AS assigned_last_name'));		

		return $task->getTasks($sql);		
	}
	
	/**
	 * Returns all the tasks that are starting on $date
	 * @param string $id
	 * @param string $date
	 */
	public function getAssignedTaskByDate($id, $date)
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->select()->setIntegrityCheck(false)->from(array('t'=>$task->getTableName()));
		$sql = $sql->where('assigned_to = ?', $id)->where(new Zend_Db_Expr('date_format(t.end_date,"%Y-%m-%d")').' = ?', $date);
		$sql = $sql->orwhere('assigned_to = ?', $id)->where(new Zend_Db_Expr('date_format(t.start_date,"%Y-%m-%d")').' = ?', $date);	
		
		$sql = $sql->joinRight(array('p' => 'projects'), 'p.id = t.project_id', array('name AS project_name'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = t.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
		$sql = $sql->joinLeft(array('u3' => 'users'), 'u3.id = t.assigned_to', array('first_name AS assigned_first_name', 'last_name AS assigned_last_name'));		

		return $task->getTasks($sql);		
	}	
	
	/**
	 * Returns an array of all the tasks for a given user. If $project then the array only contains tasks for that project
	 * @param int $id
	 * @param int $project
	 * @return mixed
	 */
	public function getOpenAssignedTasks($id, $project = FALSE, $overdue = FALSE)
	{
		$sql = $this->db->select()->from(array('t'=> 'tasks'));
		$sql = $sql->where(array('assigned_to' => $id,'progress != 100', 't.end_date != \'0000-00-00 00:00:00\''));
		if($project)
		{
			$sql = $sql->where(array ('project_id' => $project));	
		}
		$sql = $sql->where('t.status != 4');
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = t.project_id', array('project_name' => 'name'));
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = t.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), $sql::JOIN_LEFT);
		$sql = $sql->join(array('u3' => 'users'), 'u3.id = t.assigned_to', array('assigned_first_name' => 'first_name','assigned_last_name' => 'last_name'), $sql::JOIN_LEFT);

		$sql->where(array('p.status NOT IN(4,5,6)'));
		$sql = $sql->order('t.end_date DESC');
		return $this->getRows($sql);		
	}
}