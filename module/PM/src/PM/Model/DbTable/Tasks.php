<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/models/DbTable/Tasks.php
*/

/**
 * PM - Tasks DB Model
 *
 * Interacts with the Tasks database table
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/models/DbTable/Tasks.php
 */
class PM_Model_DbTable_Tasks extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "tasks";
	
	/**
	 * Returns the Tasks table name
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
		'parent' => $data['parent'],
		'milestone' => $data['milestone'],
		'assigned_to' => $data['assigned_to'],
		'project_id' => $data['project_id'],
		'progress' => $data['progress'],
		'duration' => $data['duration'],
		'hours_worked' => $data['hours_worked'],
		'start_date' => $data['start_date'],
		'end_date' => $data['end_date'],
		'status' => $data['status'],
		'percent_complete' => $data['percent_complete'],
		'description' => $data['description'],
		'notify' => ($data['notify'] != '' ? $data['notify'] : '0'),
		'priority' => $data['priority'],
		'type' => $data['type'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Project
	 * @param $where
	 * @return mixed
	 */
	public function getTask($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$task_data = $data->toArray();
		return $task_data;
	}
	
	/**
	 * Returns more than 1 Company
	 * @param $where
	 * @return mixed
	 */
	public function getTasks($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$tasks = $data->toArray();		
		return $tasks;
	}
	
	/**
	 * Update a Company
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateTask($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Company
	 * @param $data
	 * @return int
	 */
	public function addTask($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Company
	 * @param $id
	 * @return bool
	 */
	function deleteTask($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col . ' = ?', $id);
		return $this->delete($where);
	}	
	
}