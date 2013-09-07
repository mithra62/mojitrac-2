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
use Application\Model\AbstractModel;

 /**
 * PM - User Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/modules/pm/models/User.php
 */
class Permissions extends AbstractModel
{
	private $permissions;
	
	public $cache_key = 'permissions';
	
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db)
	{
		parent::__construct($adapter, $db);
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
			$this->permissions = array();
			
			$sql = $this->db->select()->from(array('urp'=>'user_role_permissions')) 
					->columns(array('name'));
			
			$sql = $sql->join('user_role_2_permissions', 'user_role_2_permissions.permission_id = urp.id');
			$sql = $sql->join('user2role', 'user2role.role_id = user_role_2_permissions.role_id');
			
			$sql = $sql->where(array('user2role.user_id' => $id));
			$perms = $this->getRows($sql);
			foreach($perms As $perm)
			{
				$this->permissions[] = $perm['name'];
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