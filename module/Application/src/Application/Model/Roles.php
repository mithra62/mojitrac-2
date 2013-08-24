<?php
/**
 * Roles Model
 * @author Eric
 *
 */
class PM_Model_Roles extends Model_Abstract
{
	
	public static $permissions = FALSE;
	
	private $ur_2_perm = 'user_role_2_permissions';
	
	public $cache_key = 'roles';
	
	public function __construct()
	{
		parent::__construct();	
	}
	
	/**
	 * Returns the User Form
	 * @return object
	 */
	public function getRolesForm($options = array())
	{
        return new PM_Form_Roles($options);	
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
		$user = new PM_Model_DbTable_User_Roles;
		$sql = $user->select()->setIntegrityCheck(false)->from(array('r'=>$user->getTableName()));
		$sql = $sql->where('r.id = ?', $id);
		return $user->getUserRole($sql);
	}
	
	/**
	 * Returns an array of all user names
	 * @return mixed
	 */
	public function getAllRoleNames()
	{
		$roles = new PM_Model_DbTable_User_Roles;
		$sql = $roles->select()->from($roles->getTableName(), array('id','name'));
		return $roles->getUserRoles($sql);
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllRoles($view_type = FALSE)
	{
		$roles = new PM_Model_DbTable_User_Roles;
		$sql = $roles->select();
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where('type = ?', $view_type);
		}
		return $roles->getUserRoles($sql);		
	}
	
	/**
	 * Returuns all the users that belong to a role
	 * @param int $id
	 * @return array
	 */
	public function getUsersOnRole($id)
	{
		$users = new PM_Model_DbTable_Users;
		$sql = $users->select()->setIntegrityCheck(false)->from(array('u' => $users->getTableName()), array('u.*'));
		$sql = $sql->join(array('u2r' => 'user2role'), 'u2r.user_id = u.id AND u2r.role_id = '.$id, array());
		return $users->getUsers($sql);
	}
	
	/**
	 * Returns all the permissions a given role has attached to it
	 * @param int $id
	 * @return array
	 */
	public function getRolePermissions($id, $return = 'keys')
	{
		$perm = new PM_Model_DbTable_User_Role_Permissions;
		$sql = $perm->select()->setIntegrityCheck(false)->from(array('p' => $this->ur_2_perm), array('p.*'))->where('role_id = ?', $id);
		if($return == 'assoc')
		{
			$sql = $sql->join(array('urp' => 'user_role_permissions'), 'p.permission_id = urp.id', array('name'));
		}
		
		$perms = $perm->getPermissions($sql);
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
		$perm = new PM_Model_DbTable_User_Role_Permissions;
		$sql = $perm->select();
		return $perm->getPermissions($sql);
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
		$linker = new PM_Model_DbTable_User_Role_To_Permissions;
		
		//remove old permissions
		$linker->deletePermissions($id);
		//add a new set
		foreach($perms AS $perm)
		{
			if(isset($data[$perm['name']]) && $data[$perm['name']] == '1')
			{
				//add the permission
				$linker->addPermission($id, $perm['id']);	
			}
		}
	    $this->cache->clean(
	          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
	          array('permissions', $this->cache_key)
	    );	
		    
		return TRUE;
	}
	
	/**
	 * Updates a Role
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateRole($data, $id)
	{
		$role = new PM_Model_DbTable_User_Roles;
		$sql = $role->getSQL($data);
		if($role->update($sql, "id = '$id'"))
		{
			if($this->addRolePermissions($data, $id))
			{
			    $this->cache->clean(
			          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
			          array('permissions', $this->cache_key)
			    );				
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
	public function updateUserRoles($roles, $id)
	{
		$role = new PM_Model_DbTable_User_To_Role;
		$role->deleteUserRole($id);
		foreach($roles AS $new_role)
		{
			$sql = $role->getSQL(array('user_id'=> $id, 'role_id'=>$new_role));
			$role->addUserToRole($sql);
		}
	    $this->cache->clean(
	          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
	          array('permissions', $this->cache_key)
	    );			
		return TRUE;		
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