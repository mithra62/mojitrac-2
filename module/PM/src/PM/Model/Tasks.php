<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./module/PM/src/PM/Model/Tasks.php
 */

namespace PM\Model;

use Application\Model\AbstractModel;

 /**
 * PM - Tasks Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Tasks.php
 */
class Tasks extends AbstractModel
{
	/**
	 * For passing to the DB
	 * @var array
	 */
	private $sql_where = array();
	
	/**
	 * For passing to the DB
	 * @var array
	 */
	private $sql_not = array();	

	/**
	 * The key to use for the cache items
	 * @var string
	 */
	public $cache_key = 'tasks';

	/**
	 * The Tasks Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Returns the Artist Form
	 * @return object
	 */
	public function getTaskForm($id, $options = array(), $hidden = array())
	{
        return new PM_Form_Task($id, $options, $hidden);		
	}	
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getTaskIdByName($name)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $this->db->getTask($sql);
	}
	
	/**
	 * Returns a task for a given task $id
	 * @param int $id
	 * @param arrray $what
	 * @return array
	 */
	public function getTaskById($id, array $what = null)
	{
		$sql = $this->db->select();
		
		if(is_array($what))
		{
			$sql->from(array('t'=> 'tasks'))->columns(what);
		}
		else
		{
			$sql->from(array('t'=> 'tasks'));
		}
		
		$sql = $sql->where(array('t.id' => $id));
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = t.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = t.assigned_to', array('assigned_first_name' => 'first_name', 'assigned_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = p.company_id', array('company_id' => 'id', 'company_name' => 'name'), 'left');
		
		return $this->getRow($sql);
	}
	
	/**
	 * Returns an array of all unique artist names
	 * @return mixed
	 */
	public function getAllTaskNames()
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('name'))
								->where('status = ?', 'active');
		return $this->db->getTasks($sql);
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllTasks($view_type = FALSE)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('t'=>$this->db->getTableName()));
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where('p.status = ?', $view_type);
		}
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = t.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = t.assigned_to', array('first_name AS assigned_first_name', 'last_name AS assigned_last_name'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = t.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
			
		return $this->db->getTasks($sql);		
	}
	
	public function getTasksByStartDate($date)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('t'=>$this->db->getTableName()));
		$sql = $sql->where(new Zend_Db_Expr('date_format(t.start_date,"%Y-%m-%d")').' = ?', $date);
		$sql = $sql->orwhere(new Zend_Db_Expr('date_format(t.end_date,"%Y-%m-%d")').' = ?', $date);
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = t.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = t.assigned_to', array('first_name AS assigned_first_name', 'last_name AS assigned_last_name'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = t.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));		
		
		return $this->db->getTasks($sql);
	}
	
	/**
	 * Returns the name and id for the tasks on the $project_id
	 * @param int $project_id
	 * @return array
	 */
	public function getTaskOptions($project_id = FALSE)
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->select()->from($task->getTableName(), array('name', 'id'));
		$sql = $sql->where('status != ?', '6');
		if($project_id)
		{
			$sql = $sql->where('project_id = ?', $project_id);
		}
		
		$sql = $sql->order('name ASC');
		return $task->getTasks($sql);
	}
	
	/**
	 * Returns the tasks for a company
	 * @param int $id
	 * @return array
	 */
	public function getTasksByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where = array();	
		$where['company_id'] = $id;
		return $this->getTasksWhere($where, $not);
	}

	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @param array $where
	 * @return array
	 */
	public function getTasksByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['project_id'] = $id;
		return $this->getTasksWhere($where, $not);
	}
	
	/**
	 * Returns all the tasks used for the daily notification email
	 * @param int $id
	 * @return array
	 */
	public function getUserDtrTasks($id, array $where = null, array $not = null)
	{
		
	}
	
	/**
	 * Returns all the tasks a user is related to. By default will only include tasks user is assigned to.
	 * @param int $id
	 * @param bool $inc_created
	 * @param bool $inc_owned
	 * @param bool $inc_archived
	 * @return array
	 */
	public function getTasksByUserId($id, $inc_created = FALSE, $inc_archived = FALSE, $inc_assigned_to = FALSE)
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->select()->setIntegrityCheck(false)->from(array('t'=>$task->getTableName()));
		
		if($inc_created)
		{
			if($inc_archived)
			{
				$sql->orwhere("creator = ? ", $id);
			}
			else
			{
				$sql->orwhere("creator = ? AND status != '6'", $id);
			}
		}
		
		if($inc_assigned_to)
		{
			if($inc_archived)
			{
				$sql->orwhere("assigned_to = ? ", $id);
			}
			else
			{
				$sql->orwhere("assigned_to = ? AND status != '6'", $id);
			}
		}		
		
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = t.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
		$sql = $sql->joinLeft(array('u3' => 'users'), 'u3.id = t.assigned_to', array('first_name AS assigned_first_name', 'last_name AS assigned_last_name'));		
		return $task->getTasks($sql);
	}

	private function getTasksWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->from(array('t'=> 'tasks'));
		
		if(is_array($where))
		{
			foreach($where AS $key => $value)
			{
				$sql = $sql->where(array($key => $value));
			}
		}
		
		if(is_array($not))
		{
			foreach($not AS $key => $value)
			{
				$sql = $sql->where("$key != '$value'");
			}
		}
		
		if(is_array($orwhere))
		{
			foreach($orwhere AS $key => $value)
			{
				$sql = $sql->orwhere("$key = ? ", $value);
			}
		}
		
		if(is_array($ornot))
		{
			foreach($ornot AS $key => $value)
			{
				$sql = $sql->orwhere("$key != ? ", $value);
			}
		}		
		
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = t.creator', array('creator_first_name' => 'first_name','creator_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('u3' => 'users'), 'u3.id = t.assigned_to', array('assigned_first_name' => 'first_name','assigned_last_name' => 'last_name'), 'left');
		return $this->getRows($sql);
		
	}

	
	/**
	 * Inserts or updates a Company
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addTask($data, $bypass_update = FALSE)
	{
		$ext = $this->event('pre.moji_task_add', $this, compact('data'));
		if($ext->stopped()) return $ext->last();
				
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->getSQL($data);	
		$sql['creator'] = $data['creator'];
		$task_id = $task->addTask($sql);	

		$ext = $this->event('post.moji_task_add', $this, compact('task_id', 'data'));
		if($ext->stopped()) return $ext->last();		
				
		return $task_id;
	}
	
	/**
	 * Updates a task
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateTask(array $data, $id)
	{	
		$ext = $this->event('pre.moji_task_update', $this, compact('id', 'data'));
		if($ext->stopped()) return $ext->last();
		
        if((is_numeric($data['start_hour']) && $data['start_hour'] <= 24) 
		&& (is_numeric($data['start_minute']) && $data['start_minute'] <= 60))
		{	
			$data['start_date'] = $data['start_date'].' '.$data['start_hour'].':'.$data['start_minute'];
		}
		
		if((is_numeric($data['end_hour']) && $data['end_hour'] <= 24) 
		&& (is_numeric($data['end_minute']) && $data['end_minute'] <= 60))
		{
			$data['end_date'] = $data['end_date'].' '.$data['end_hour'].':'.$data['end_minute'];
		}
		
		if($data['status'] == '5' || $data['status'] == '6')
		{
			$data['progress'] = '100';
		} 
		elseif($data['progress'] == '100' && $data['status'] != '6')
		{
			$data['status'] = '5';
		}	

		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->getSQL($data);
		$data = $task->update($sql, "id = '$id'");
		
		if($data)
		{
			$ext = $this->event('post.moji_task_update', $this, compact('id', 'data'));
			if($ext->stopped()) return $ext->last();
			return $data;
		}
	}
	
	/**
	 * Updates the company id for a given $task_id; grabs it if given $project_id
	 * @param int $task_id
	 * @param int $company_id
	 * @param int $project_id
	 * @return int
	 */
	public function updateCompanyId($task_id, $company_id = FALSE, $project_id = FALSE)
	{
		if($project_id && !$company_id)
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$company_id = $project->getCompanyIdById($project_id);
		}
		
		$task = new PM_Model_DbTable_Tasks;
		$sql = array('company_id' => $company_id);
		return $task->update($sql, "id = '$task_id'");
	}
	
	/**
	 * Updates the file count for a given $id
	 * @param int 	 $id
	 * @param int 	 $count
	 * @param string $col
	 * @return bool
	 */
	public function updateTaskFileCount($id, $count = 1, $col = 'file_count')
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = array($col => new Zend_Db_Expr($col.'+'.$count));
		return $task->updateTask($sql, $id);
	}

	/**
	 * Updates the hours_worked for a given $id
	 * @param int 	$id
	 * @param floar $time
	 * @return bool
	 */
	public function updateTaskTime($id, $time)
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = array('hours_worked' => new Zend_Db_Expr('hours_worked+'.$time));
		return $task->updateTask($sql, $id);		
	}	
	
	/**
	 * Handles everything for a campaign to stop tracking a Last.fm Album Profile.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeTask($task_id)
	{
		$ext = $this->event('pre.moji_task_remove', $this, compact('task_id'));
		if($ext->stopped()) return $ext->last();
				
		$task = new PM_Model_DbTable_Tasks;
		$remove = $task->deleteTask($task_id);
		
		$ext = $this->event('post.moji_task_remove', $this, compact('task_id'));
		if($ext->stopped()) return $ext->last();

		return $remove;
	}
	
	/**
	 * Removes all the tasks for the given $company_id
	 * @param int $company_id
	 * @return bool
	 */
	public function removeTasksByCompany($company_id)
	{
		$task = new PM_Model_DbTable_Tasks;
		return $task->deleteTask($company_id, 'company_id');		
	}

	/**
	 * Removes all the tasks for the given $company_id
	 * @param int $company_id
	 * @return bool
	 */
	public function removeTasksByProject($project_id)
	{
		$task = new PM_Model_DbTable_Tasks;
		return $task->deleteTask($project_id, 'project_id');		
	}

	/**
	 * Returns the total files a task has
	 * @param int $id
	 * @param int $status
	 * @return int
	 */
	public function getFileCount($id, $status = FALSE)
	{
		$file = new PM_Model_DbTable_Files;
		$sql = $file->select()
					->from($file->getTableName(), array(new Zend_Db_Expr('COUNT(id) AS count')))
					->where('task_id = ?', $id);
		$data = $file->getFile($sql);
		if(is_array($data))
		{
			return $data['count'];
		}		
	}	
	
	/**
	 * Splits up the task start and end dates to their indivual parts
	 * @param array $arr
	 * @return array
	 */
	public function parseTaskDates(array $arr)
	{
		if(array_key_exists('start_date', $arr))
		{
			$temp = $arr['start_date'];
			$parts = explode(' ',$temp);
			
			if(count($parts) > 1)
			{			
				list($hours, $minute, $seconds) = explode(':', $parts['1']);
	
				$arr['start_hour'] = $hours;
				$arr['start_minute'] = $minute;
				$arr['start_date'] = $parts['0'];
			}
		}
		
		if(array_key_exists('end_date', $arr))
		{
			$temp = $arr['end_date'];
			$parts = explode(' ',$temp);
			
			if(count($parts) > 1)
			{
				list($hours, $minute, $seconds) = explode(':', $parts['1']);
	
				$arr['end_hour'] = $hours;
				$arr['end_minute'] = $minute;
				$arr['end_date'] = $parts['0'];					
			}				
		}
		
		return $arr;
	}
	
	/**
	 * Logs the assignment of the task to the user
	 * @param int $id
	 * @param int $assigned_to
	 * @param int $assigned_by
	 * @param str $desc
	 * @return bool
	 */
	public function logTaskAssignment($id, $assigned_to, $assigned_by, $assign_comment = null)
	{
		$assignment = new PM_Model_DbTable_Task_Assignments;
		$sql = $assignment->getSQL(array('task_id'=>$id, 'assigned_to' => $assigned_to, 'assigned_by' => $assigned_by, 'assign_comment' => $assign_comment));
		return $assignment->addTaskAssignment($sql);
		
	}
	
	/**
	 * Returns the assignment history for a given task
	 * @param int $id
	 * @return array
	 */
	public function getTaskAssignments($id)
	{
		$sql = $this->db->select()->from(array('ta'=> 'task_assignments'));
		$sql = $sql->where(array('task_id' => $id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = ta.assigned_to', array('to_first_name' => 'first_name', 'to_last_name' => 'last_name'));
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = ta.assigned_by', array('by_first_name' => 'first_name', 'by_last_name' => 'last_name'));
		return $this->getRows($sql);
	}
	
	/**
	 * Wrapper to mark a task "completed" along with progress value
	 * @param int $id
	 * @param int $identity
	 */
	public function markCompleted($id, $identity)
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = array('progress' => '100', 'status' => '5', 'last_modified' => new Zend_Db_Expr('NOW()'));
		return $task->updateTask($sql, $id);
	}
	
	public function updateProgress($id, $progress)
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = array('progress' => $progress, 'last_modified' => new Zend_Db_Expr('NOW()'));
		if($progress != 0)
		{
			$sql['status'] = '3';
		}	
		if($progress == 100)
		{
			$sql['status'] = '5';
		}				
		return $task->updateTask($sql, $id);		
	}
	
	public function getProjectEstimatedTime($id)
	{
		$sql = $this->db->select()
					->from(array('t' => 'tasks'), array('estimate_time' => new \Zend\Db\Sql\Expression('SUM(duration)')))
					->where(array('project_id' => $id))
					->where(array('t.status' => 4));
					
		$data = $this->getRow($sql);
		if($data && is_array($data))
		{
			return $data['estimate_time'];
		}
		return 0;
	}
	
	public function autoArchive()
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = array('status' => '6');
		$date = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
		$date = date('Y-m-d H:i:s', $date);
		
		$where = array();
		$where[] = $task->getAdapter()->quoteInto('status = ?', 5);
		$where[] = $task->getAdapter()->quoteInto('last_modified < ?', $date);
		return $task->update($sql, $where);
	}
	
	public function getByProjectIdTaskName($name, $project_id)
	{
		$sql = $this->db->select()->from(array('t' => $this->db->getTableName()), array('id'))->where('project_id = ?', $project_id)->where('name LIKE ?', $name);
		return $this->db->getTask($sql);		
	}
}