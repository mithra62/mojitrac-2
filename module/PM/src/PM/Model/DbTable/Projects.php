<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Projects.php
*/

/**
* PM - Projects Database Model
*
* Returns the Projects Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Projects.php
*/
class PM_Model_DbTable_Projects extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "projects";
	
	/**
	 * Returns the Projects Database Model
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
		'company_id' => $data['company_id'],
		'start_date' => $data['start_date'],
		'end_date' => $data['end_date'],
		'actual_end_date' => $data['actual_end_date'],
		'status' => $data['status'],
		'percent_complete' => $data['percent_complete'],
		'description' => $data['description'],
		'target_budget' => $data['target_budget'],
		'actual_budget' => $data['actual_budget'],
		'creator' => $data['creator'],
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
	public function getProject($where)
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
	 * Returns more than 1 Project
	 * @param $where
	 * @return mixed
	 */
	public function getProjects($where)
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
	 * Update a Project
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateProject($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Project
	 * @param $data
	 * @return int
	 */
	public function addProject($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Project
	 * @param $id
	 * @return bool
	 */
	function deleteProject($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col. ' = ?', $id);
		return $this->delete($where);
	}
}