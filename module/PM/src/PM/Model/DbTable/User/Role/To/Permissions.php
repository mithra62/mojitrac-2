<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/Role/To/Permissions.php
*/

/**
* PM - User Role To Permissions Database Model
*
* Returns the User Role To Permissions Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/Role/To/Permissions.php
*/
class PM_Model_DbTable_User_Role_To_Permissions extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "user_role_2_permissions";
	
	/**
	 * Returns the User Role To Permissions Table name
	 * @return string
	 */	
	public function getTableName()
	{
		return $this->_name;
	}	
    
	/**
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
		'role_id' => $data['role_id'],
		'permission_id' => $data['permission_id']
		);
	}
	
	/**
	 * Returns an array of Contacts
	 * @param $where
	 * @return mixed
	 */
	public function getPermission($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		return $data->toArray();;
	}
	
	/**
	 * Returns more than 1 Contact
	 * @param $where
	 * @return mixed
	 */
	public function getPermissions($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$companies = $data->toArray();		
		return $companies;
	}
	
	/**
	 * Update a Contact
	 * @param $data
	 * @param $id
	 * @return mixed
	 */
	public function updatePermission($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}
	
	/**
	 * Adds a Contact
	 * @param $data
	 * @return int
	 */
	public function addPermission($role_id, $perm_id)
	{
		$data = $this->getSQL(array('role_id' => $role_id, 'permission_id' =>$perm_id));
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Contact
	 * @param $id
	 * @return bool
	 */
	function deletePermissions($id)
	{
		$where = parent::getAdapter()->quoteInto('role_id = ?', $id);
		return $this->delete($where);
	}	
	
}