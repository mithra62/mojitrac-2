<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/File/Reviews.php
*/

/**
* PM - File Reviews Database Model
*
* Returns the File Reviews Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/File/Reviews.php
*/
class PM_Model_DbTable_File_Reviews extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "file_reviews";
	
	/**
	 * Returns the File Review Table name
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
		'review' => $data['review'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Files Reviews
	 * @param $where
	 * @return mixed
	 */
	public function getFileReview($where)
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
	 * Returns more than 1 File Review
	 * @param $where
	 * @return mixed
	 */
	public function getFileReviews($where)
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
	 * Update a File Review
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateFileReview($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a File Review
	 * @param $data
	 * @return int
	 */
	public function addFileReview($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a File Review
	 * @param $id
	 * @return bool
	 */
	function deleteFileReview($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col. ' = ?', $id);
		return $this->delete($where);
	}
}