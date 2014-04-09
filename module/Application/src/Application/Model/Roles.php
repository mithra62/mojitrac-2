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

use Zend\Db\Sql\Sql;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

 /**
 * PM - User Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/modules/pm/models/User.php
 */
class Roles extends AbstractModel
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
	 * The validation filters
	 * @var object
	 */
	protected $inputFilter;
	
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
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
			'name' => $data['name'],
			'description' => $data['description'],
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
	 * Role CRUD Validation logic
	 * @return object
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));			
			
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}	
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getUsersIdByName($name)
	{
		$user = new PM_Model_DbTable_Users;
		$sql = $user->select()
					  ->from($user->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $user->getUsers($sql);
	}
	
	/**
	 * Returns an individual user array
	 * @param int $id
	 * @return array
	 */
	public function getRoleById($id)
	{
		$sql = $this->db->select()->from(array('r'=>'user_roles'))->where(array('r.id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Returns an array of all user names
	 * @return mixed
	 */
	public function getAllRoleNames()
	{
		$sql = $this->db->select()->from('user_roles')->columns(array('id','name'));
		return $this->getRows($sql);		
	}
	
	/**
	 * Returns an array of all the created User Roles
	 * @param string $view_type
	 * @return array
	 */
	public function getAllRoles($view_type = FALSE)
	{
		$sql = $this->db->select()->from('user_roles');
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where(array('type' => $view_type));
		}
		
		return $this->getRows($sql);		
	}
	
	/**
	 * Returuns all the users that belong to a role
	 * @param int $id
	 * @return array
	 */
	public function getUsersOnRole($id)
	{
		$sql = $this->db->select()->from(array('u' => 'users'))
					->join(array('u2r' => 'user2role'), 'u2r.user_id = u.id', array())
					->where(array('u2r.role_id' => $id));
		return $this->getRows($sql);
	}
	
	/**
	 * Returns all the permissions a given role has attached to it
	 * @param int $id
	 * @return array
	 */
	public function getRolePermissions($id, $return = 'keys')
	{
		$sql = $this->db->select()->from(array('p' => 'user_role_2_permissions'), array('p.*'))->where(array('role_id' => $id));
		if($return == 'assoc')
		{
			$sql = $sql->join(array('urp' => 'user_role_permissions'), 'p.permission_id = urp.id', array('name'));
		}
		
		$perms = $this->getRows($sql);
		$p_arr = array();
		foreach($perms AS $p)
		{
			if($return == 'keys')
			{
				$p_arr[] = $p['permission_id'];
			}
			
			if($return == 'assoc')
			{
				$p_arr[$p['name']] = 1;
			}
		}
		return $p_arr;
	}
	
	/**
	 * Returns all the permissions available to the system
	 * @return array
	 */
	public function getAllPermissions()
	{
		$sql = $this->db->select()->from('user_role_permissions');
		return $this->getRows($sql);
	}	
	
	/**
	 * Inserts or updates a user
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addRole($data)
	{
		$perms = $this->getAllPermissions();
		$role = new PM_Model_DbTable_User_Roles;
		$sql = $role->getSQL($data);
		if($role_id = $role->addUserRole($sql))
		{
			$this->addRolePermissions($data, $role_id);
		    $this->cache->clean(
		          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
		          array('permissions', $this->cache_key)
		    );				
			return $role_id;	
		}
	}
	
	public function addRolePermissions($data, $id)
	{
		$perms = $this->getAllPermissions();
		
		//remove old permissions
		$this->deleteRolePermissions($id);
		
		//add a new set
		foreach($perms AS $perm)
		{
			if(isset($data[$perm['name']]) && $data[$perm['name']] == '1')
			{
				//add the permission
				$insert = array('role_id' => $id, 'permission_id' => $perm['id']);
				$this->insert('user_role_2_permissions', $insert);
			}
		}
		
		return TRUE;
	}
	
	public function deleteRolePermissions($role_id)
	{
		return $this->remove('user_role_2_permissions', array('role_id' => $role_id));
	}
	
	/**
	 * Updates a Role
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateRole($data, $id)
	{
		$sql = $this->getSQL($data);
		if($this->update('user_roles', $sql, array('id' => $id)))
		{
			if($this->addRolePermissions($data, $id))
			{			
				return TRUE;
			}
		}
	}
	
	/**
	 * Updates a users roleset 
	 * @param array $roles
	 * @param int $id
	 * @return bool
	 */
	public function updateUsersRoles($user_id, array $roles)
	{
		$this->removeUsersRoles($user_id);
		foreach($roles AS $new_role)
		{
			$sql = array(
				'user_id' => $user_id,
				'role_id' => $new_role
			);
			$this->insert('user2role', $sql);
		}	
		
		return TRUE;		
	}
	
	/**
	 * Removes a user from all groups
	 * @param int $user_id
	 * @return number
	 */
	public function removeUsersRoles($user_id)
	{
		return $this->remove('user2role', array('user_id' => $user_id));
	}
	
	/**
	 * Handles everything for removing a role.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeRole($id)
	{
		
		$role = new PM_Model_DbTable_User_Roles;
		if($role->deleteUserRole($id))
		{
			$linker = new PM_Model_DbTable_User_Role_To_Permissions;
			$linker->deletePermissions($id);
		    $this->cache->clean(
		          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
		          array('permissions', $this->cache_key)
		    );			
			return TRUE;
		}
	}
	
	public function getUserRoles()
	{
		$roles = new PM_Model_DbTable_User_Roles;
		$sql = $roles->select();
		return $roles->getUserRoles($sql);
	}	
}