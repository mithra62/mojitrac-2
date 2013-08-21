<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Files.php
*/

/**
* PM - Files Database Model
*
* Returns the Files Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Files.php
*/
class PM_Model_DbTable_Files extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "files";
	
	/**
	 * Returns the Files Database Model
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
		'status' => $data['status'],
		'creator' => $data['creator'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Project
	 * @param $where
	 * @return mixed
	 */
	public function getFile($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$file_data = $data->toArray();
		return $file_data;
	}
	
	/**
	 * Returns more than 1 Company
	 * @param $where
	 * @return mixed
	 */
	public function getFiles($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$files = $data->toArray();		
		return $files;
	}
	
	/**
	 * Update a Company
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateFile($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Company
	 * @param $data
	 * @return int
	 */
	public function addFile($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Company
	 * @param $id
	 * @return bool
	 */
	function deleteFile($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col . ' = ?', $id);
		return $this->delete($where);
	}	
	
}