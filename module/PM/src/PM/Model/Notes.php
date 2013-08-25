<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./module/PM/src/PM/Model/Notes.php
 */

namespace PM\Model;

use Application\Model\AbstractModel;

 /**
 * PM - Times Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Notes.php
 */
class Notes extends AbstractModel
{
	/**
	 * The Notes Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}	
	
	/**
	 * Returns the Bookmark Form
	 * @return object
	 */
	public function getNoteForm($options = array(), $hidden = array())
	{
        return new PM_Form_Note($options, $hidden);		
	}	
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getNoteIdByName($name)
	{
		$bk = new PM_Model_DbTable_Bookmarks;
		$sql = $bk->select()
					  ->from($bk->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $bk->getTask($sql);
	}
	
	/**
	 * Returns a bookmark for a given task $id
	 * @param int $id
	 * @return array
	 */
	public function getNoteById($id)
	{
		$note = new PM_Model_DbTable_Notes;
		$sql = $note->select()->setIntegrityCheck(false)->from(array('n'=>$note->getTableName()));
		$sql = $sql->where('n.id = ?', $id);
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = n.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('t' => 'tasks'), 't.id = n.task_id', array('name AS task_name'));
		$sql = $sql->joinLeft(array('c' => 'companies'), 'c.id = n.company_id', array('name AS company_name'));
		return $note->getNote($sql);
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllNotes($view_type = FALSE)
	{
		$note = new PM_Model_DbTable_Notes;
		$sql = $note->select()->setIntegrityCheck(false)->from(array('n'=>$note->getTableName()));
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = n.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = n.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
			
		return $note->getNotes($sql);		
	}
	
	/**
	 * Returns the notes for a company
	 * @param int $id
	 * @return array
	 */
	public function getNotesByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['n.company_id'] = $id;
		return $this->getNotesWhere($where, $not);	
	}

	/**
	 * Returns the notes that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getNotesByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['n.project_id'] = $id;
		return $this->getNotesWhere($where, $not);
	}

	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getNotesByTaskId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['n.task_id'] = $id;
		return $this->getNotesWhere($where, $not);
	}
	
	/**
	 * Returns the tasks that belong to a project
	 * @param int $id
	 * @return array
	 */
	public function getNotesByUserId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['n.owner'] = $id;
		return $this->getNotesWhere($where, $not);
	}	

	private function getNotesWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->from(array('n'=> 'notes'));
		
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
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = n.project_id', array('project_name' => 'name', 'project_id' => 'id'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = n.task_id', array('task_name' => 'name'), 'left');
		return $this->getRows($sql);
	}	

	
	/**
	 * Inserts or updates a Note
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addNote($data, $creator)
	{
		$note = new PM_Model_DbTable_Notes;
		$sql = $note->getSQL($data);
		$sql['company_id'] = (array_key_exists('company', $data) ? $data['company'] : 0);
		$sql['project_id'] = (array_key_exists('project', $data) ? $data['project'] : 0);
		$sql['task_id'] = (array_key_exists('task', $data) ? $data['task'] : 0);
		$note_id = $note->addNote($sql);
		return $note_id;
	}
	
	/**
	 * Updates a Note
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateNote($data, $id)
	{
		$note = new PM_Model_DbTable_Notes;
		$sql = $note->getSQL($data);
		return $note->update($sql, "id = '$id'");
	}	
	
	/**
	 * Handles everything for removing a note.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeNote($id)
	{
		$note = new PM_Model_DbTable_Notes;
		return $note->deleteNote($id);
	}
	
	/**
	 * Deletes a "Note" record from the database based on $company_id
	 * @param $company_id
	 * @return bool
	 */
	public function removeNotesByCompany($company_id)
	{
		$note = new PM_Model_DbTable_Notes;
		return $note->deleteNote($company_id, 'company_id');
	}
	
	/**
	 * Deletes a "Note" record from the database based on $project_id
	 * @param $project_id
	 * @return bool
	 */
	public function removeNotesByProject($project_id)
	{
		$note = new PM_Model_DbTable_Notes;
		return $note->deleteNote($project_id, 'project_id');
	}		
}