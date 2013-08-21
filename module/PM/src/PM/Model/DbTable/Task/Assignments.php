<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Task/Assignments.php
*/

/**
* PM - Task Assignment Database Model
*
* Returns the Task Assignment Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Task/Assignments.php
*/
class PM_Model_DbTable_Task_Assignments extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "task_assignments";

	/**
	 * Returns the Task Assignment Table name
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
		'task_id' => $data['task_id'],
		'assigned_by' => $data['assigned_by'],
		'assigned_to' => $data['assigned_to'],
		'comments' => $data['assign_comment'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Task Assignment
	 * @param $where
	 * @return mixed
	 */
	public function getTaskAssignment($where)
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
	 * Returns more than 1 Task Assignment
	 * @param $where
	 * @return mixed
	 */
	public function getTaskAssignments($where)
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
	 * Update a Task Assignment
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateTaskAssignment($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Task Assignment
	 * @param $data
	 * @return int
	 */
	public function addTaskAssignment($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Task Assignment
	 * @param $id
	 * @return bool
	 */
	function deleteTaskAssignment($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col . ' = ?', $id);
		return $this->delete($where);
	}	
	
}