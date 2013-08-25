<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/modules/pm/models/User.php
 */

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Application\Model\AbstractModel;
use Application\Model\Hash;

 /**
 * PM - User Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/modules/pm/models/User.php
 */
class User extends AbstractModel
{		
	/**
	 * The cache object
	 * @var object
	 */
	public $cache;	
	
	/**
	 * Contains all the permissions
	 * @var array
	 */
	public static $permissions = FALSE;
	
	/**
	 * The User Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db)
	{
		parent::__construct($adapter, $db);
	}
		
	/**
	 * Returns the User Form
	 * @return object
	 */
	public function getUsersForm($options = array(), $add_password = FALSE, $add_terms = FALSE, $unique_email = TRUE, $add_roles = FALSE)
	{
        return new PM_Form_Users($options, $add_password, $add_terms, $unique_email, $add_roles);		
	}	
	
	/**
	 * Returns the Password Form
	 * @return object
	 */
	public function getPasswordForm($options = array(), $confirm = TRUE)
	{
        return new PM_Form_Password($options, $confirm);		
	}
	
	/**
	 * Returns the Prefernces Form
	 * @return object
	 */
	public function getPrefsForm($options = array(), $confirm = TRUE)
	{
        return new PM_Form_Prefs($options, $confirm);		
	}	
	
	public function changePassword($id, $password)
	{
		$hash = new Hash;
		$salt = $hash->gen_salt();
		$sql = array('hash' => $salt, 'password' => $hash->password($password, $salt), 'forgotten_hash' => '');
		return $this->db->updateUser($sql, $id);
	}
	
	/**
	 * Verifies that the provided credentials are accurate after salting, hashing and db checking.
	 * @param string $email
	 * @param string $password
	 */
	public function verifyCredentials($key, $password, $col = 'email')
	{
		$salt = $this->getHash($key, $col);
		$hash = new Hash;
		$sql = $this->db->select()->from(array('u'=> 'users'))->columns(array('id'))
				->where(array($col => $key))
				->where(array('user_status' => 'd'))
				->where(array('password' => $hash->password($password, $salt)));
		return $this->getRow($sql);
	}
	
	/**
	 * Returns the hash field for password comparisons
	 * @param string $key
	 * @param string $col
	 */
	public function getHash($key, $col = 'id')
	{
		$sql = $this->db->select()->from(array('u' => 'users'))->columns(array('hash'))->where(array($col => $key));
		$hash = $this->getRow($sql);
		if(array_key_exists('hash', $hash))
		{
			return $hash['hash'];
		}
	}	
	
	/**
	 * Returns the id for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getUsersIdByName($name)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $this->db->getUsers($sql);
	}
	
	/**
	 * Returns a user array by email address
	 * @param string $email
	 */
	public function getUserByEmail($email)
	{
		$sql = $this->db->select()->from('users')->where(array('email' => $email));
		return $this->getRow($sql);
	}
	
	/**
	 * Returns a user array by email address
	 * @param string $email
	 */
	public function getUserByHarvestId($harvest_id)
	{
		$sql = $this->db->select()->where('harvest_id = ?', $harvest_id);
		return $this->db->getUser($sql);
	}
	
	/**
	 * Returns all the users that were imported from Harvest
	 */
	public function getHarvestUsers()
	{
		$sql = $this->db->select()->where("harvest_id != ''");
		return $this->db->getUsers($sql);
	}
	
	/**
	 * Returns a user array by password hash
	 * @param string $email
	 */
	public function getUserByPwHash($hash, $expired = TRUE)
	{
		$sql = $this->db->select()->where('forgotten_hash = ?', $hash);
		if($expired)
		{
			$sql = $sql->where('pw_forgotten > ?', date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-1, date("Y"))));
			$sql = $sql->where('pw_forgotten < ?', date('Y-m-d H:i:s'));
		}
		return $this->db->getUser($sql);
	}	
	
	/**
	 * Returns an individual user array
	 * @param int $id
	 * @return array
	 */
	public function getUserById($id)
	{
		$sql = $this->db->select()->from(array('u'=>'users'));
		$sql = $sql->where(array('u.id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Returns an array of all user names
	 * @return mixed
	 */
	public function getAllUsersNames()
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('id','name'));
		return $this->db->getCompanies($sql);
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllUsers($status = FALSE)
	{
		$sql = $this->db->select();
		
		if($status != '')
		{
			$sql = $sql->where('user_status = ?', $status);
		}
		return $this->db->getUsers($sql);		
	}
	
	/**
	 * Inserts or updates a user
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addUser($data)
	{
		$hash = new Model_Hash;
		$sql = $this->db->getSQL($data);
		$sql['hash'] = $hash->gen_salt();
		$sql['password'] = $hash->password($data['password'], $sql['hash']);
		if($user_id = $this->db->addUser($sql))
		{
			if(isset($data['user_roles']))
			{
				$roles = new PM_Model_Roles;
				$roles->updateUserRoles($data['user_roles'], $user_id);
			}
			return $user_id;		
		}
		
	}
	
	/**
	 * Updates a user
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateUser($data, $id)
	{
		$sql = $this->db->getSQL($data);
		if($this->db->update($sql, "id = '$id'"))
		{
			if(isset($data['user_roles']))
			{				
				$roles = new PM_Model_Roles;
				$roles->updateUserRoles($data['user_roles'], $id);
			    $this->cache->remove($id.'_permissions');				
			}
			return TRUE;
		}
	}
	
	/**
	 * Updates the login time for the user
	 * @param int $id
	 */
	public function upateLoginTime($id)
	{		
		$sql = array(
				'last_login' => new \Zend\Db\Sql\Expression("NOW()"),
				'last_modified' => new \Zend\Db\Sql\Expression("NOW()")
		);	
		$where = array('id' => $id);
		return $this->update('users', $sql, $where);
	}
	
	/**
	 * Updates the password hash for the user
	 * @param int $id
	 */
	public function upatePasswordHash($id, $hash)
	{
		$sql = array(
				'pw_forgotten' => new \Zend\Db\Sql\Expression("NOW()"), 
				'forgotten_hash' => $hash, 
				'last_modified' => new \Zend\Db\Sql\Expression("NOW()")
		);
		
		$where = array('id' => $id);
		return $this->update('users', $sql, $where);
	}	
	
	/**
	 * Handles everything for removing a user.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeUser($id)
	{
		//check if the user has any attachments
		$projets = $this->getAssignedProjectIds($id);
		$tasks = $this->getAssignedTasks($id);
		if($projets || $tasks)
		{
			if($this->db->updateUser(array('user_status' => '0'), $id))
			{
				return TRUE;
			}
		}
		else
		{
			
			if($this->db->deleteUser($id))
			{
				return TRUE;
			}			
		}
	}
	
	/**
	 * Returns all the roles a user is attached to
	 * @param int $id
	 * @return array
	 */
	public function getUserRoles($id)
	{
		$roles = new PM_Model_DbTable_User_Roles;
		$sql = $roles->select()->setIntegrityCheck(false)->from(array('r' => $roles->getTableName()), 'r.*');
		$sql = $sql->join(array('u2r' => 'user2role'), 'u2r.role_id = r.id AND u2r.user_id = '.$id);
		return $roles->getUserRoles($sql);
	}
	
	/**
	 * Returns the roles a user $id is attached to as a simple, one dimentional, array
	 * @param int $id
	 * @return array
	 */
	public function getUserRolesArr($id)
	{
		$roles = new PM_Model_DbTable_User_Roles;
		$sql = $roles->select()->setIntegrityCheck(false)->from(array('r' => $roles->getTableName()), 'r.id');
		$sql = $sql->join(array('u2r' => 'user2role'), 'u2r.role_id = r.id AND u2r.user_id = '.$id, array());
		$user_roles = $roles->getUserRoles($sql);
		$return = array();
		foreach($user_roles As $role)
		{
			$return[] = $role['id'];
		}
		return $return;
	}
	
	/**
	 * Returns all the roles a user is attached to
	 * @param int $id
	 * @return array
	 */
	public function getAllUserRoles($id)
	{
		$users = new PM_Model_DbTable_User_;
		$sql = $users->select();
		return $users->getUsers($sql);
	}	
	
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
		$proj_team = new PM_Model_DbTable_Projects_Teams;
		$sql = $proj_team->select()->from($proj_team->getTableName(), array('project_id'))->where('user_id = ?', $id);
		$proj_ids = $proj_team->getProjectTeamMembers($sql);
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
	 * @param unknown_type $id
	 * @param unknown_type $upcoming
	 * @return unknown_type
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
		$sql = $sql->where(array('assigned_to' => $id))->where('progress != 100 AND t.end_date != ?', '0000-00-00 00:00:00');
		if($project)
		{
			$sql = $sql->where('project_id = $project');	
		}
		$sql = $sql->where('t.status != 4');
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = t.project_id', array('project_name' => 'name'));
		$sql = $sql->join(array('u2' => 'users'), 'u2.id = t.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), $sql::JOIN_LEFT);
		$sql = $sql->join(array('u3' => 'users'), 'u3.id = t.assigned_to', array('assigned_first_name' => 'first_name','assigned_last_name' => 'last_name'), $sql::JOIN_LEFT);

		$sql->where(array('p.status NOT IN(4,5,6)'));
		$sql = $sql->order('t.end_date DESC');
		return $this->getRows($sql);		
	}
	
	private function getUsersWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('u'=>$this->db->getTableName()));
		
		if(is_array($where))
		{
			foreach($where AS $key => $value)
			{
				$sql = $sql->where("$key = ? ", $value);
			}
		}
		
		if(is_array($not))
		{
			foreach($not AS $key => $value)
			{
				$sql = $sql->where("$key != ? ", $value);
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
		
		return $this->db->getUsers($sql);
		
	}	
	
	public function getUserByFirstLastName($first, $last)
	{
		$sql = $this->db->select()->from(array('u' => $this->db->getTableName()), array('id'))->where('first_name LIKE ?', $first)->where('last_name LIKE ?', $last);
		return $this->db->getUser($sql);		
	}
	
	public function checkPreference($id, $pref, $default = FALSE)
	{
		$ud = new PM_Model_User_Data;
		$data = $ud->getUsersData($id);
		if($data)
		{
			if(isset($data[$pref]))
			{
				return $data[$pref];
			}
			else
			{
				return $data[$pref] = $default;
			}
		}
	}
}