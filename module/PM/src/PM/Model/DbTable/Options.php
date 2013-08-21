<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/models/DbTable/Options.php
*/

/**
 * PM - Options DB Model
 *
 * Interacts with the Options database table
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/models/DbTable/Options.php
 */
class PM_Model_DbTable_Options extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "options";
	
	/**
	 * Returns the Options table name
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
		'area' => $data['area'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Option
	 * @param $where
	 * @return mixed
	 */
	public function getOption($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$project_data = $data->toArray();
		return $project_data;
	}
	
	/**
	 * Returns more than 1 Option
	 * @param $where
	 * @return mixed
	 */
	public function getOptions($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$projects = $data->toArray();		
		return $projects;
	}
	
	/**
	 * Update an Option
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateOption($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds an Option
	 * @param $data
	 * @return int
	 */
	public function addOption($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes an Option
	 * @param $id
	 * @return bool
	 */
	function deleteOption($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col. ' = ?', $id);
		return $this->delete($where);
	}
}