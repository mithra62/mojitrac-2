<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/ActivityLog.php
*/

/**
* PM - Activity Log Database Model
*
* Returns the Activity Log  Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/ActivityLog.php
*/
class PM_Model_DbTable_ActivityLog extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "activity_logs";
	
	/**
	 * Returns the Activity Log Database Model
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
		'type' => $data['type'],
		'performed_by' => $data['performed_by'],
		'stuff' => Zend_Json::encode($data['stuff']),
		'company_id' => $data['company_id'],
		'project_id' => $data['project_id'],
		'task_id' => $data['task_id'],
		'note_id' => $data['note_id'],
		'bookmark_id' => $data['bookmark_id'],
		'user_id' => $data['user_id'],
		'file_id' => $data['file_id'],
		'file_rev_id' => $data['file_rev_id'],
		'file_review_id' => $data['file_review_id'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of Log
	 * @param $where
	 * @return mixed
	 */
	public function getLog($where)
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
	 * Returns more than 1 Log entry
	 * @param $where
	 * @return mixed
	 */
	public function getLogs($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$activity = $data->toArray();		
		return $activity;
	}
	
	/**
	 * Update a Log entry
	 * @param $data
	 * @param $id
	 * @return mixed
	 */
	public function updateLog($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}
	
	/**
	 * Adds a Log entry
	 * @param $data
	 * @return int
	 */
	public function addLog($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Log entry
	 * @param $id
	 * @return bool
	 */
	function deleteLog($id)
	{
		$where = parent::getAdapter()->quoteInto('id = ?', $id);
		return $this->delete($where);
	}	
	
}