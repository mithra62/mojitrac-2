<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/File/Revisions.php
*/

/**
* PM - File Revisions Database Model
*
* Returns the File Revisions Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/File/Revisions.php
*/
class PM_Model_DbTable_File_Revisions extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "file_revisions";
	
	/**
	 * Returns the File Revisions Table name
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
		'file_name' => $data['file_name'],
		'stored_name' => $data['stored_name'],
		'size' => $data['size'],
		'extension' => $data['extension'],
		'mime_type' => $data['mime_type'],
		'description' => $data['description'],
		'status' => $data['status'],
		'approver' => $data['approver'],
		'uploaded_by' => $data['uploaded_by'],
		'approval_comment' => $data['approval_comment'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Files Revisions
	 * @param $where
	 * @return mixed
	 */
	public function getFileRevision($where)
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
	 * Returns more than 1 File Revision
	 * @param $where
	 * @return mixed
	 */
	public function getFileRevisions($where)
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
	 * Update a File Revision
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateFileRevision($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a File Revision
	 * @param $data
	 * @return int
	 */
	public function addFileRevision($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a File Revision
	 * @param $id
	 * @return bool
	 */
	function deleteFileRevision($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col. ' = ?', $id);
		return $this->delete($where);
	}
}