<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/Roles.php
*/

/**
* PM - User Roles Database Model
*
* Returns the User Roles Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/Roles.php
*/
class PM_Model_DbTable_User_Roles extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "user_roles";
	
	/**
	 * Returns the User Roles Table name
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
	 * Returns more than 1 Contact
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
		
		$companies = $data->toArray();		
		return $companies;
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
	 * Adds a Contact
	 * @param $data
	 * @return int
	 */
	public function addUserRole($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Contact
	 * @param $id
	 * @return bool
	 */
	function deleteUserRole($id)
	{
		$where = parent::getAdapter()->quoteInto('id = ?', $id);
		return $this->delete($where);
	}	
	
}