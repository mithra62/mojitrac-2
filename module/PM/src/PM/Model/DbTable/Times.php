<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Times.php
*/

/**
* PM - Times Database Model
*
* Returns the Times Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Times.php
*/
class PM_Model_DbTable_Times extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "times";
	
	/**
	 * Returns the Times table name
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
		'date' => $data['date'],
		'company_id' => $data['company_id'],
		'user_id' => $data['user_id'],
		'project_id' => $data['project_id'],
		'task_id' => $data['task_id'],
		'hours' => $data['hours'],
		'billable' => $data['billable'],
		'description' => $data['description'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Time set
	 * @param $where
	 * @return mixed
	 */
	public function getTime($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$bk_data = $data->toArray();
		return $bk_data;
	}
	
	/**
	 * Returns more than 1 Bookmark
	 * @param $where
	 * @return mixed
	 */
	public function getTimes($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		return $data->toArray();
	}
	
	/**
	 * Update a Bookmark
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateTime($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Bookmark
	 * @param $data
	 * @return int
	 */
	public function addTime($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Bookmark
	 * @param $id
	 * @return bool
	 */
	function deleteTime($id)
	{
		$where = parent::getAdapter()->quoteInto('id = ?', $id);
		return $this->delete($where);
	}	
	
}