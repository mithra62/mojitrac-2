<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/To/Role.php
*/

/**
* PM - User To Role Database Model
*
* Returns the User To Role Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/To/Role.php
*/
class PM_Model_DbTable_User_To_Role extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "user2role";
	
	/**
	 * Returns the User To Role Table name
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
		'user_id' => $data['user_id'],
		'role_id' => $data['role_id']
		);
	}
	
	/**
	 * Returns an array of a useres roles
	 * @param $where
	 * @return mixed
	 */
	public function getUserRole($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		return $data->toArray();;
	}
	
	/**
	 * Returns more than 1 user role
	 * @param $where
	 * @return mixed
	 */
	public function getUserRoles($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		return $data->toArray();
	}
	
	/**
	 * Update a User Role
	 * @param $data
	 * @param $id
	 * @return mixed
	 */
	public function updateUserRole($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}
	
	/**
	 * Adds a User to a Role
	 * @param $data
	 * @return int
	 */
	public function addUserToRole($data)
	{
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Users roles
	 * @param $id
	 * @return bool
	 */
	function deleteUserRole($id)
	{
		$where = parent::getAdapter()->quoteInto('user_id = ?', $id);
		return $this->delete($where);
	}	
	
}