<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/models/DbTable/Companies.php
*/

/**
 * PM - Companies DB Model
 *
 * Interacts with the Companies database table
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/models/DbTable/Companies.php
 */
class PM_Model_DbTable_Companies extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "companies";
	
	/**
	 * Returns the Companies table name
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
		'phone1' => $data['phone1'],
		'phone2' => $data['phone2'],
		'fax' => $data['fax'],
		'address1' => $data['address1'],
		'address2' => $data['address2'],
		'city' => $data['city'],
		'state' => $data['state'],
		'zip' => $data['zip'],
		'primary_url' => $data['primary_url'],
		'description' => $data['description'],
		'type' => $data['type'],
		'custom' => $data['custom'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of Company
	 * @param $where
	 * @return mixed
	 */
	public function getCompany($where)
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
	 * Returns more than 1 Company
	 * @param $where
	 * @return mixed
	 */
	public function getCompanies($where)
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
	 * Update a Company
	 * @param $data
	 * @param $id
	 * @return mixed
	 */
	public function updateCompany($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}
	
	/**
	 * Adds a Company
	 * @param $data
	 * @return int
	 */
	public function addCompany($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Company
	 * @param $id
	 * @return bool
	 */
	function deleteCompany($id)
	{
		$where = parent::getAdapter()->quoteInto('id = ?', $id);
		return $this->delete($where);
	}	
	
}