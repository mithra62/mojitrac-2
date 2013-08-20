<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/UsersTable.php
*/

namespace PM\Model\DbTable;

use Zend\Db\TableGateway\TableGateway;

/**
* PM - UsersTable Database Model
*
* Returns the UsersTable Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/UsersTable.php
*/
class UsersTable 
{
	protected $tableGateway;
	
   /**
     * Table name
     * @var string
     */
	protected $_name = "users";
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
		
	/**
	 * Returns the UsersTable table name
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
		'email' => (!empty($data['email']) ? $data['email'] : ''),
		'first_name' => (!empty($data['first_name']) ? $data['first_name'] : ''),
		'last_name' => (!empty($data['last_name']) ? $data['last_name'] : ''),
		'phone_mobile' => (!empty($data['phone_mobile']) ? $data['phone_mobile'] : ''),
		'phone_home' => (!empty($data['phone_home']) ? $data['phone_home'] : ''),
		'phone_work' => (!empty($data['phone_work']) ? $data['phone_work'] : ''),
		'phone_fax' => (!empty($data['phone_fax']) ? $data['phone_fax'] : ''),
		'job_title' => (!empty($data['job_title']) ? $data['job_title'] : ''),
		'jabber' => (!empty($data['jabber']) ? $data['jabber'] : ''),
		'aol' => (!empty($data['aol']) ? $data['aol'] : ''),
		'yahoo' => (!empty($data['yahoo']) ? $data['yahoo'] : ''),
		'google_talk' => (!empty($data['google_talk']) ? $data['google_talk'] : ''),
		'msn' => (!empty($data['msn']) ? $data['msn'] : ''),
		'ichat' => (!empty($data['ichat']) ? $data['ichat'] : ''),
		'skype' => (!empty($data['skype']) ? $data['skype'] : ''),
		'description' => (!empty($data['description']) ? $data['description'] : ''),
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of Contacts
	 * @param $where
	 * @return mixed
	 */
	public function getUser($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$company_data = $data->toArray();
		return $company_data;
	}
	
	/**
	 * Returns more than 1 Contact
	 * @param $where
	 * @return mixed
	 */
	public function getUsers($where)
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
	public function updateUser($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}
	
	/**
	 * Adds a Contact
	 * @param $data
	 * @return int
	 */
	public function addUser($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Contact
	 * @param $id
	 * @return bool
	 */
	function deleteUser($id)
	{
		$where = parent::getAdapter()->quoteInto('id = ?', $id);
		return $this->delete($where);
	}	
	
}