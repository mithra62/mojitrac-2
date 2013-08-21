<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/modules/pm/models/Bookmarks.php
 */

 /**
 * PM - Bookmarks Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/modules/pm/models/Bookmarks.php
 */
class PM_Model_Bookmarks extends Model_Abstract
{	
	/**
	 * 
	 * @param PM_Model_DbTable_Bookmarks $db
	 */
	public function __construct(PM_Model_DbTable_Bookmarks $db)
	{
		parent::__construct();
		$this->db = $db;
	}
	
	/**
	 * Returns the Bookmark Form
	 * @return object
	 */
	public function getBookmarkForm($options = array())
	{
        return new PM_Form_Bookmark($options);		
	}	
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getBookmarkIdByName($name)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $this->db->getTask($sql);
	}
	
	/**
	 * Returns a bookmark for a given task $id
	 * @param int $id
	 * @return array
	 */
	public function getBookmarkById($id)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('bk'=>$this->db->getTableName()));
		$sql = $sql->where('bk.id = ?', $id);
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = bk.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = bk.owner', array('first_name AS owner_first_name', 'last_name AS owner_last_name'));
		$sql = $sql->joinLeft(array('t' => 'tasks'), 't.id = bk.task_id', array('name AS task_name'));
		$sql = $sql->joinLeft(array('c' => 'companies'), 'c.id = bk.company_id', array('name AS company_name'));
		return $this->db->getBookmark($sql);
	}
	
	/**
	 * Returns an array of all unique artist names
	 * @return mixed
	 */
	public function getAllBookmarkNames()
	{
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->select()->from($task->getTableName(), array('name'))
								->where('status = ?', 'active');
		return $task->getTasks($sql);
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllBookmarks($view_type = FALSE)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('b'=>$this->db->getTableName()));
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where('p.status = ?', $view_type);
		}
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = b.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = b.owner', array('first_name AS owner_first_name', 'last_name AS owner_last_name'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = b.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
			
		return $this->db->getBookmarks($sql);		
	}
	
	/**
	 * Returns the tasks for a company
	 * @param int $id
	 * @return array
	 */
	public function getBookmarksByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['bk.company_id'] = $id;
		return $this->getBookmarksWhere($where, $not);	
	}

	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getBookmarksByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['bk.project_id'] = $id;
		return $this->getBookmarksWhere($where, $not);
	}

	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getBookmarksByTaskId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['bk.task_id'] = $id;
		return $this->getBookmarksWhere($where, $not);
	}
	
	/**
	 * Returns the bookmarks that belong to a user
	 * @param int $id
	 * @return array
	 */
	public function getBookmarksByUserId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['bk.owner'] = $id;
		return $this->getBookmarksWhere($where, $not);
	}
	
	private function getBookmarksWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('bk'=>$this->db->getTableName()));
		
		if(is_array($where))
		{
			foreach($where AS $key => $value)
			{
				$sql = $sql->where("$key = ? ", $value);
			}
		}
		
		if(is_array($not))
		{
			foreach($not AS $key => $value)
			{
				$sql = $sql->where("$key != ? ", $value);
			}
		}
		
		if(is_array($orwhere))
		{
			foreach($orwhere AS $key => $value)
			{
				$sql = $sql->orwhere("$key = ? ", $value);
			}
		}
		
		if(is_array($ornot))
		{
			foreach($ornot AS $key => $value)
			{
				$sql = $sql->orwhere("$key != ? ", $value);
			}
		}		
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = bk.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('t' => 'tasks'), 't.id = bk.task_id', array('name AS task_name'));
		return $this->db->getBookmarks($sql);	
	}	

	
	/**
	 * Inserts or updates a Company
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addBookmark($data)
	{
		$ext = $this->event('pre.moji_bookmark_add', $this, compact('data'));
		if($ext->stopped()) return $ext->last();
				
		$sql = $this->db->getSQL($data);
		$sql['company_id'] = (array_key_exists('company', $data) ? $data['company'] : 0);
		$sql['project_id'] = (array_key_exists('project', $data) ? $data['project'] : 0);
		$sql['task_id'] = (array_key_exists('task', $data) ? $data['task'] : 0);		
		$bookmark_id = $this->db->addBookmark($sql);
		
		$ext = $this->event('post.moji_bookmark_add', $this, compact('data', 'bookmark_id'));
		if($ext->stopped()) return $ext->last();
				
		return $bookmark_id;
	}
	
	/**
	 * Updates a company
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateBookmark($data, $id)
	{
		$ext = $this->event('pre.moji_bookmark_edit', $this, compact('data'));
		if($ext->stopped()) return $ext->last();
				
		$sql = $this->db->getSQL($data);
		$return = $this->db->update($sql, "id = '$id'");
		
		$ext = $this->event('pre.moji_bookmark_edit', $this, compact('data', 'id'));
		if($ext->stopped()) return $ext->last();	

		return $return;
	}	
	
	/**
	 * Removes a Bookmark based on the $id.
	 * @param $id
	 * @return bool
	 */
	public function removeBookmark($id)
	{
		return $this->db->deleteBookmark($id);
	}
	
	/**
	 * Removes all the Bookmarks based on the $company_id.
	 * @param $company_id
	 * @return bool
	 */
	public function removeBookmarksByCompany($company_id)
	{
		return $this->db->deleteBookmark($company_id, 'company_id');
	}

	/**
	 * Removes all the Bookmarks based on the $project_id.
	 * @param $project_id
	 * @return bool
	 */
	public function removeBookmarksByProject($project_id)
	{
		return $this->db->deleteBookmark($project_id, 'project_id');
	}	
}