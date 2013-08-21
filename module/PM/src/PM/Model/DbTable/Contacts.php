<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Contacts.php
*/

/**
* PM - Contacts Database Model
*
* Returns the Contacts Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Contacts.php
*/
class PM_Model_DbTable_Contacts extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "company_contacts";
	
	/**
	 * Returns the Contacts Database Model
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
		'job_title' => $data['job_title'],
		'company_id' => $data['company_id'],
		'first_name' => $data['first_name'],
		'last_name' => $data['last_name'],
		'title' => $data['title'],
		'email' => $data['email'],
		'email2' => $data['email2'],
		'url' => $data['url'],
		'phone_home' => $data['phone_home'],
		'phone2' => $data['phone2'],
		'fax' => $data['fax'],
		'mobile' => $data['mobile'],
		'address1' => $data['address1'],
		'address2' => $data['address2'],
		'city' => $data['city'],
		'state' => $data['state'],
		'zip' => $data['zip'],
		'description' => $data['description'],
		'jabber' => $data['jabber'],
		'icq' => $data['icq'],
		'msn' => $data['msn'],
		'yahoo' => $data['yahoo'],
		'aol' => $data['aol'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of Contacts
	 * @param $where
	 * @return mixed
	 */
	public function getContact($where)
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
	public function getContacts($where)
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
	public function updateContact($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}
	
	/**
	 * Adds a Contact
	 * @param $data
	 * @return int
	 */
	public function addContact($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Contact
	 * @param $id
	 * @return bool
	 */
	function deleteContact($id)
	{
		$where = parent::getAdapter()->quoteInto('id = ?', $id);
		return $this->delete($where);
	}	
	
}