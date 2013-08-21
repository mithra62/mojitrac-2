<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Projects/Teams.php
*/

/**
* PM - Project Teams Database Model
*
* Returns the Project Teams Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Projects/Teams.php
*/
class PM_Model_DbTable_Projects_Teams extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "project_teams";
	
	/**
	 * Returns the Project Teams Table name
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
		'project_id' => $data['project_id'],
		'user_id' => $data['user_id'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Project
	 * @param $where
	 * @return mixed
	 */
	public function getProjectTeamMember($where)
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
	public function getProjectTeamMembers($where)
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
	public function updateProjectTeamMember($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Project
	 * @param $data
	 * @return int
	 */
	public function addProjectTeamMember($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Project
	 * @param $id
	 * @return bool
	 */
	function deleteProjectTeamMember($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col. ' = ?', $id);
		return $this->delete($where);
	}
}