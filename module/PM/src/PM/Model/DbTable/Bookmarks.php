<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Bookmarks.php
*/

/**
* PM - Bookmarks Database Model
*
* Returns the Bookmarks Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Bookmarks.php
*/
class PM_Model_DbTable_Bookmarks extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "bookmarks";
	
	/**
	 * Returns the Bookmarks Database Model
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
		'owner' => $data['owner'],
		'name' => $data['name'],
		'url' => $data['url'],
		'description' => $data['description'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Project
	 * @param $where
	 * @return mixed
	 */
	public function getBookmark($where)
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
	public function getBookmarks($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$bk = $data->toArray();		
		return $bk;
	}
	
	/**
	 * Update a Bookmark
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateBookmark($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Bookmark
	 * @param $data
	 * @return int
	 */
	public function addBookmark($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Bookmark
	 * @param $id
	 * @return bool
	 */
	function deleteBookmark($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col. ' = ?', $id);
		return $this->delete($where);
	}	
	
}