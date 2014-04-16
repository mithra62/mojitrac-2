<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./moji/application/modules/pm/models/User.php
 */

namespace Application\Model;

use Zend\Db\Sql\Sql;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;
use Application\Model\Hash;

 /**
 * PM - User Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/modules/pm/models/User.php
 */
class Users extends AbstractModel
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
	 * The validation filter for the password form
	 * @var object
	 */
	protected $passwordInputFilter;

	/**
	 * The validation filter for the user registration form
	 * @var object
	 */
	protected $registrationInputFilter;	
	
	/**
	 * The Roles Model
	 * @var object
	 */
	public $roles = null;
	
	/**
	 * The User Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db, \Application\Model\Roles $roles)
	{
		parent::__construct($adapter, $db);
		$this->roles = $roles;
	}
	
	/**
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
			'email' => (!empty($data['email']) ? $data['email'] : ''),
			'first_name' => (!empty($data['first_name']) ? $data['first_name'] : ''),
			'last_name' => (!empty($data['last_name']) ? $data['last_name'] : ''),
			'phone_mobile' => (!empty($data['phone_mobile']) ? $data['phone_mobile'] : ''),
			'phone_home' => (!empty($data['phone_home']) ? $data['phone_home'] : ''),
			'phone_work' => (!empty($data['phone_work']) ? $data['phone_work'] : ''),
			'phone_fax' => (!empty($data['phone_fax']) ? $data['phone_fax'] : ''),
			'job_title' => (!empty($data['job_title']) ? $data['job_title'] : ''),
			'description' => (!empty($data['description']) ? $data['description'] : ''),
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	/**
	 * Sets the InputFilter
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Change Password specific validation logic
	 * @param int $identity
	 * @param \Application\Model\Hash $hash
	 * @param bool $confirm
	 * @return object
	 */
	public function getPasswordInputFilter($identity, \Application\Model\Hash $hash, $confirm = TRUE)
	{
		if (!$this->passwordInputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			if($confirm)
			{
				$inputFilter->add($factory->createInput(array(
					'name'     => 'old_password',
					'required' => true,
					'filters'  => array(
						array('name' => 'StripTags'),
						array('name' => 'StringTrim'),
					),
					'validators' => array(
						array(
							'name' => '\Application\Validate\Password\Match',
							'options' => array(
								'identity' => $identity,
								'users' => $this,
								'hash' => $hash
							)
						),
					),
				)));
			}
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'new_password',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'Identical',
						'options' => array(
							'token' => 'confirm_password',
							'strict' => FALSE
						)
					),
				),
			)));

			$inputFilter->add($factory->createInput(array(
				'name'     => 'confirm_password',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));			
			
			$this->passwordInputFilter = $inputFilter;
		}
	
		return $this->passwordInputFilter;
	}	
	
	/**
	 * Sets the Input Filter for the registration form
	 * @return object
	 */
	public function getRegistrationInputFilter()
	{
		if (!$this->registrationInputFilter) {
			
			$inputFilter = new InputFilter();
			$factory = new InputFactory();			
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'email',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'EmailAddress',
					),
					array(
						'name' => 'Db\NoRecordExists',
						'options' => array(
							'table' => 'users',
						    'field' => 'email',
							'adapter' => $this->adapter
						)
					),
				),
			)));

			$inputFilter->add($factory->createInput(array(
				'name'     => 'first_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));

			$inputFilter->add($factory->createInput(array(
				'name'     => 'last_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));

			$inputFilter->add($factory->createInput(array(
				'name'     => 'password',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));

			$inputFilter->add($factory->createInput(array(
				'name'     => 'confirm_password',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'Identical',
						'options' => array(
							'token' => 'password',
							'strict' => FALSE
						)
					),
				),
			)));
			
			$this->registrationInputFilter = $inputFilter;
		}
	
		return $this->registrationInputFilter;
	}
	
	/**
	 * Sets the Input Filter for the registration form
	 * @return object
	 */
	public function getEditInputFilter()
	{
		if (!$this->registrationInputFilter) {
				
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'first_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'last_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
				
			$this->registrationInputFilter = $inputFilter;
		}
	
		return $this->registrationInputFilter;
	}	
	
	/**
	 * Changes a users password
	 * @param int $id
	 * @param string $password
	 * @return Ambigous <\Zend\Db\Adapter\Driver\StatementInterface, \Zend\Db\ResultSet\Zend\Db\ResultSet, \Zend\Db\Adapter\Driver\ResultInterface, \Zend\Db\ResultSet\Zend\Db\ResultSetInterface>
	 */
	public function changePassword($id, $password)
	{
		$hash = new Hash;
		$salt = $hash->gen_salt();
		$sql = array('hash' => $salt, 'password' => $hash->password($password, $salt), 'forgotten_hash' => '');
		return $this->update('users', $sql, array('id' => $id) );
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
		$sql = $this->db->select()->from(array('u'=> 'users'))->where(array('forgotten_hash' => $hash));
		if($expired)
		{
			$where = $sql->where->greaterThan('pw_forgotten', date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-1, date("Y"))))->where->lessThan('pw_forgotten', date('Y-m-d H:i:s'));
			$sql = $sql->where($where);
		}
		
		return $this->getRow($sql);
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
		$sql = $this->db->select()->from('users');
		
		if($status != '')
		{
			$sql = $sql->where(array('user_status' => $status));
		}
		
		return $this->getRows($sql);		
	}
	
	/**
	 * Creates a member
	 * @param array $data
	 * @param \Application\Model\Hash $hash
	 * @param \Application\Model\Roles $roles
	 * @return unknown
	 */
	public function addUser(array $data, \Application\Model\Hash $hash, \Application\Model\Roles $roles)
	{
		$ext = $this->trigger(self::EventUserAddPre, $this, compact('data'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
		
		$sql = $this->getSQL($data);
		$sql['hash'] = $hash->gen_salt();
		$sql['password'] = $hash->password($data['password'], $sql['hash']);
		$user_id = $data['user_id'] = $this->insert('users', $sql);
		
		if($user_id)
		{
			if(isset($data['user_roles']))
			{
				$roles->updateUsersRoles($user_id, $data['user_roles']);
			}
			
			$ext = $this->trigger(self::EventUserAddPost, $this, compact('user_id', 'data'), $this->setXhooks($data));
			if($ext->stopped()) return $ext->last(); elseif($ext->last()) $user_id = $ext->last();

			return $user_id;			
		}
	}
	
	/**
	 * Updates a user
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateUser($data, $user_id)
	{
		$ext = $this->trigger(self::EventUserUpdatePre, $this, compact('data', 'user_id'), $this->setXhooks($data));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
				
		$sql = $this->getSQL($data);
		if($this->update('users', $sql, array('id' => $user_id)))
		{
			if(isset($data['user_roles']))
			{
				$this->roles->updateUsersRoles($user_id, $data['user_roles']);			
			}
			
			$ext = $this->trigger(self::EventUserUpdatePost, $this, compact('user_id', 'data'), $this->setXhooks($data));
			if($ext->stopped()) return $ext->last(); elseif($ext->last()) $user_id = $ext->last();			
			
			return $user_id;
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
	public function removeUser($user_id)
	{		
		$ext = $this->trigger(self::EventUserRemovePre, $this, compact('user_id'), array($user_id));
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $user_id = $ext->last();
				
		//check if the user has any attachments
		$projets = $this->getAssignedProjectIds($user_id);
		$tasks = $this->getOpenAssignedTasks($user_id);
		if($projets || $tasks)
		{
			if($this->update('users', array('user_status' => '0'), array('id' => $user_id)))
			{
				$ext = $this->trigger(self::EventUserRemovePost, $this, compact('user_id'), array($user_id));
				if($ext->stopped()) return $ext->last(); elseif($ext->last()) $user_id = $ext->last();
				return $user_id;
			}
		}
		else
		{
			
			if($this->remove('users', array('id' => $user_id)))
			{
				$ext = $this->trigger(self::EventUserRemovePost, $this, compact('user_id'), array($user_id));
				if($ext->stopped()) return $ext->last(); elseif($ext->last()) $user_id = $ext->last();
				return $user_id;
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
		$sql = $this->db->select()->from(array('r' => 'user_roles'), 'r.*');
		$sql = $sql->join(array('u2r' => 'user2role'), 'u2r.role_id = r.id');
		$sql = $sql->where(array('u2r.user_id' => $id));
		return $this->getRows($sql);
	}
	
	/**
	 * Returns the roles a user $id is attached to as a simple, one dimentional, array
	 * @param int $id
	 * @return array
	 */
	public function getUserRolesArr($id)
	{			
		$sql = $this->db->select()->from(array('r' => 'user_roles'))->columns(array('id' => 'id'));
		$sql = $sql->join(array('u2r' => 'user2role'), 'u2r.role_id = r.id ')->where(array('u2r.user_id' => $id));
		$user_roles = $this->getRows($sql);
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