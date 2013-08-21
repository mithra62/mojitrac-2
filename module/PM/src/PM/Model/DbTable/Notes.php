<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Notes.php
*/

/**
* PM - Notes Database Model
*
* Returns the Notes Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Notes.php
*/
class PM_Model_DbTable_Notes extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "notes";
	
	/**
	 * Returns the Notes Database Model
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
		'topic' => $data['topic'],
		'date' => $data['date'],
		'subject' => $data['subject'],
		'description' => $data['description'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Project
	 * @param $where
	 * @return mixed
	 */
	public function getNote($where)
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
	public function getNotes($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		return $data->toArray();
	}
	
	/**
	 * Update a Note
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateNote($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Note
	 * @param $data
	 * @return int
	 */
	public function addNote($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Note
	 * @param $id
	 * @return bool
	 */
	function deleteNote($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col. ' = ?', $id);
		return $this->delete($where);
	}	
	
}