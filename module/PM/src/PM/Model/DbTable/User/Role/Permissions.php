<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/Role/Permissions.php
*/

/**
* PM - User Role Permissions Database Model
*
* Returns the User Role Permissions Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/Role/Permissions.php
*/
class PM_Model_DbTable_User_Role_Permissions extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "user_role_permissions";
	
	/**
	 * Returns the User Role Permissions Table name
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
		'name' => $data['name'],
		'description' => $data['description'],
		'last_modified' => new Zend_Db_Expr('NOW()')
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
		
		return $data->toArray();
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
		
		return $data->toArray();		
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
	public function addPermission($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Contact
	 * @param $id
	 * @return bool
	 */
	function deletePermission($id)
	{
		$where = parent::getAdapter()->quoteInto('id = ?', $id);
		return $this->delete($where);
	}	
	
}