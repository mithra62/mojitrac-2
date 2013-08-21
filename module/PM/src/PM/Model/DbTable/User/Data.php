<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/Data.php
*/

/**
* PM - User Data Database Model
*
* Returns the User Data Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/User/Data.php
*/
class PM_Model_DbTable_User_Data extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "user_data";

	/**
	 * Returns the User Data Table name
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
		'option_value' => $data['option_value'],
		'option_name' => $data['option_name'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of User Data
	 * @param $where
	 * @return mixed
	 */
	public function getUserData($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		return $data->toArray();;
	}
	
	/**
	 * Returns more than 1 User Data entry
	 * @param $where
	 * @return mixed
	 */
	public function getUsersData($where)
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
	 * Update a User Data entry
	 * @param $data
	 * @param $id
	 * @return mixed
	 */
	public function updateUserData($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}
	
	/**
	 * Adds a Preference
	 * @param $data
	 * @return int
	 */
	public function addUserData($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Preference
	 * @param $id
	 * @return bool
	 */
	function deleteUserData($id)
	{
		$where = parent::getAdapter()->quoteInto('id = ?', $id);
		return $this->delete($where);
	}	
	
}