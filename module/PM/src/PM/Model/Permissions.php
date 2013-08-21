<?php
class PM_Model_Permissions extends Model_Abstract
{
	private $permissions;
	
	public $cache_key = 'permissions';
	
	public function __construct()
	{
		parent::__construct();	
		$this->db = new PM_Model_DbTable_User_Role_Permissions;
	}
	
	/**
	 * Checks a users permission
	 * @param int $id
	 * @param string $permission
	 * @param bool $redirect
	 * @return bool
	 */
	public function check($id, $permission)
	{
		// If I don't have any permissions, fetch them
		if (!$this->permissions || !is_array($this->permissions)) 
		{
			if(!$this->permissions = $this->cache->load($id.'_permissions')) 
			{
				$this->permissions = array();
				
				$sql = $this->db->select()->setIntegrityCheck(false)->from(
						array('urp'=>$this->db->getTableName()), 
						array('permission' => 'name')
				);
				$sql = $sql->joinRight(array('user_role_2_permissions'), 'user_role_2_permissions.permission_id = urp.id', array());
				$sql = $sql->joinRight(array('user2role'), 'user2role.role_id = user_role_2_permissions.role_id', array());
				
				$sql = $sql->where('user2role.user_id = ?', $id);
				$perms = $this->db->getPermissions($sql);
				foreach($perms As $perm)
				{
					$this->permissions[] = $perm['permission'];
				}
				
				$this->cache->save($this->permissions, $id.'_permissions', array($this->cache_key));
			}
		}

		if (in_array($permission ,$this->permissions))
		{
			return TRUE;
		} 
		else 
		{
			return FALSE;
		}
	}
}