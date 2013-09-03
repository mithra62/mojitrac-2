<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Event/ActivityLog.php
 */

namespace PM\Event;

use PM\Model\ActivityLog;

 /**
 * PM - Event Activity Log
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Event/ActivityLog.php
 */
class ActivityLogEvent
{	
	/**
	 * Wrapper to log a task add entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logTaskAdd(array $data, $task_id, $project_id, $performed_by)
	{
		self::logEvent(self::setDate(), 'task_add', $performed_by, $data, $project_id, 0, $task_id);
	}
	
	/**
	 * Wrapper to log a task update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logTaskUpdate(array $data, $task_id, $project_id, $performed_by)
	{
		self::logEvent(self::setDate(), 'task_update', $performed_by, $data, $project_id, 0, $task_id);
	}	
	
	/**
	 * Wrapper to log a task assignment entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logTaskAssignment(array $data, $task_id, $project_id, $performed_by)
	{
		self::logEvent(self::setDate(), 'task_assigned', $performed_by, $data, $project_id, 0, $task_id, 0, 0, $data['assigned_to']);
	}
	
	/**
	 * Wrapper to log a project removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logTaskRemove(array $data, $task_id, $project_id, $performed_by)
	{
		self::logEvent(self::setDate(), 'task_remove', $performed_by, $data, $project_id, 0, $task_id);
	}	
	
	/**
	 * Wrapper to log a project update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logProjectAdd($event)
	{
		$data = $event->getParam('data');
		$project_id = $event->getParam('project_id');
		self::logEvent(self::setDate(), 'project_add', $data['creator'], $data, $id);
	}
	
	/**
	 * Wrapper to log a project update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logProjectUpdate($event)
	{
	    $data = $event->getParam('data');
	    print_r($data);
	    echo $project_id = $event->getParam('project_id');	    
	    echo 'fdsa';
	    exit;
		self::logEvent(self::setDate(), 'project_update', $performed_by, $data, $project_id);
	}
	
	/**
	 * Wrapper to log a project removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logProjectRemove(array $data, $id, $performed_by)
	{
		self::logEvent(self::setDate(), 'project_remove', $performed_by, $data, $id);
	}
	
	/**
	 * Wrapper to log a project removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logProjectTeamRemove(array $data, $id, $performed_by)
	{
		self::logEvent(self::setDate(), 'project_team_remove', $performed_by, $data, $id);
	}

	/**
	 * Wrapper to log a project removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logProjectTeamAdd(array $data, $id, $performed_by)
	{
		self::logEvent(self::setDate(), 'project_team_add', $performed_by, $data, $id);
	}
	
	/**
	 * Wrapper to log a note update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logNoteAdd(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'note_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], $id);
	}
	
	/**
	 * Wrapper to log a note update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logNoteUpdate(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'note_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], $id);
	}
	
	/**
	 * Wrapper to log a note removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logNoteRemove(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'note_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], $id);
	}
	
	/**
	 * Wrapper to log a bookmark update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logBookmarkAdd(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'bookmark_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, $id);
	}
	
	/**
	 * Wrapper to log a bookmark update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logBookmarkUpdate(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'bookmark_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, $id);
	}
	
	/**
	 * Wrapper to log a bookmark removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logBookmarkRemove(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'bookmark_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, $id);
	}

	/**
	 * Wrapper to log a file entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileAdd(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'],0, 0, 0, $id);
	}
	
	/**
	 * Wrapper to log a file update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileUpdate(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $id);
	}
	
	/**
	 * Wrapper to log a file removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileRemove(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $id);
	}
	
	/**
	 * Wrapper to log a file revision entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileRevisionAdd(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_revision_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'],0, 0, 0, $data['file_id'], $id);
	}
	
	/**
	 * Wrapper to log a file revision update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileRevisionUpdate(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_revision_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $data['file_id'], $id);
	}
	
	/**
	 * Wrapper to log a file revision removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileRevisionRemove(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_revision_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $data['file_id'], $id);
	}

	/**
	 * Wrapper to log a file revision entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileReviewAdd(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_review_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'],0, 0, 0, $data['file_id'], $data['revision_id'], $id);
	}
	
	/**
	 * Wrapper to log a file revision update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileReviewUpdate(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_review_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $data['file_id'], $data['revision_id'], $id);
	}
	
	/**
	 * Wrapper to log a file revision removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	static public function logFileReviewRemove(array $data, $id, $performed_by)
	{
		$data = self::filterForKeys($data);
		self::logEvent(self::setDate(), 'file_review_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $data['file_id'], $data['revision_id'], $id);
	}	
	
	/**
	 * Returns the mysql formatted timestamp
	 * @return string
	 */
	static private function setDate()
	{
		return date('Y-m-d H:i:s');
	}
	
	/**
	 * Returns all the activity for a given project $id
	 * @param int $id
	 */
	public function getUsersProjectActivity($id, $filter = FALSE, $limit = 20)
	{
		$activity = new PM_Model_DbTable_ActivityLog;
		$sql = $activity->select()->setIntegrityCheck(false)->from(array('a'=>$activity->getTableName()));
		$sql->join(array('pt' => 'project_teams'), 'pt.project_id = a.project_id AND pt.user_id = '.$id, array());
				
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = a.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('t' => 'tasks'), 't.id = a.task_id', array('t.name AS task_name'));
		$sql = $sql->joinLeft(array('n' => 'notes'), 'n.id = a.note_id AND pt.project_id = n.project_id', array('subject AS note_subject'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = a.user_id', array('first_name AS user_first_name', 'last_name AS user_last_name'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = a.performed_by', array('first_name AS performed_by_first_name', 'last_name AS performed_by_last_name'));
		$sql = $sql->joinLeft(array('b' => 'bookmarks'), 'b.id = a.bookmark_id AND pt.project_id = b.project_id', array('name AS bookmark_name'));
		$sql = $sql->joinLeft(array('f' => 'files'), 'f.id = a.file_id', array('name AS file_name'));
		
		if($filter && is_array($filter))
		{
			if(array_key_exists('company_id', $filter) && is_numeric($filter['company_id']))
			{
				$sql = $sql->where('a.company_id = ?', $filter['company_id']);
			}
			
			if(array_key_exists('project_id', $filter) && is_numeric($filter['project_id']))
			{
				$sql = $sql->where('a.project_id = ?', $filter['project_id']);
			}			
		}
		
		$sql = $sql->order('a.date DESC');
		$sql = $sql->limit($limit);
		return $activity->getLogs($sql);
	}
	
	public function getActivityById($id, $filter_teams = FALSE)
	{
		$activity = new PM_Model_DbTable_ActivityLog;
		$sql = $activity->select()->setIntegrityCheck(false)->from(array('a'=>$activity->getTableName()));
		$sql = $sql->where('a.id = ?', $id);
				
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = a.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('t' => 'tasks'), 't.id = a.task_id', array('t.name AS task_name'));
		$sql = $sql->joinLeft(array('n' => 'notes'), 'n.id = a.note_id ', array('subject AS note_subject'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = a.user_id', array('first_name AS user_first_name', 'last_name AS user_last_name'));
		$sql = $sql->joinLeft(array('u2' => 'users'), 'u2.id = a.performed_by', array('first_name AS performed_by_first_name', 'last_name AS performed_by_last_name'));
		$sql = $sql->joinLeft(array('b' => 'bookmarks'), 'b.id = a.bookmark_id ', array('name AS bookmark_name'));
		$sql = $sql->joinLeft(array('f' => 'files'), 'f.id = a.file_id', array('name AS file_name'));
		return $activity->getLogs($sql);
	}
	
	private function getActivityWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$activity = new PM_Model_DbTable_ActivityLog;
		$sql = $activity->select()->setIntegrityCheck(false)->from(array('a'=>$activity->getTableName()));
		
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
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = a.project_id', array('name AS project_name', 'id AS project_id'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = a.user_id', array('first_name AS user_first_name', 'last_name AS user_last_name'));
		return $activity->getLogs($sql);	
	}	
	
	/**
	 * Takes the array and verifies the existance of the primary keys
	 * @param $data
	 */
	static private function filterForKeys(array $data)
	{
		if(array_key_exists('company', $data))
		{
			$data['company_id'] = $data['company'];
		}
		else
		{
			$data['company_id'] = 0;
		}
		
		if(array_key_exists('project', $data))
		{
			$data['project_id'] = $data['project'];
		}
		else
		{
			$data['project_id'] = 0;
		}
		
		
		if(array_key_exists('task', $data))
		{
			$data['task_id'] = $data['task'];
		}
		else
		{
			$data['task_id'] = 0;
		}		
		
		return $data;
	}

	/**
	 * Handles the logging of an event
	 * @param $date
	 * @param $type
	 * @param $performed_by
	 * @param $stuff
	 * @param $project_id
	 * @param $company_id
	 * @param $task_id
	 * @param $note_id
	 * @param $bookmark_id
	 * @param $user_id
	 * @return void
	 */
	static private function logEvent(
							$date, 
							$type, 
							$performed_by, 
							array $stuff, 
							$project_id = 0, 
							$company_id = 0, 
							$task_id = 0, 
							$note_id = 0, 
							$bookmark_id = 0, 
							$user_id = 0, 
							$file_id = 0, 
							$file_rev_id = 0, 
							$file_review_id = 0)
	{		
		$logger = new PM_Model_DbTable_ActivityLog;
		$sql = $logger->getSQL(
			   		array(
			   			 'date' => $date, 
			   			 'type' => $type, 
			   			 'performed_by' => $performed_by, 
			   			 'stuff' => $stuff,
			   			 'company_id' => $company_id,
			   			 'project_id' => $project_id,
			   			 'task_id' => $task_id,
			   			 'note_id' => $note_id,
			   			 'bookmark_id' => $bookmark_id,
			   			 'user_id' => $user_id,
			   			 'file_id' => $file_id,
			   			 'file_rev_id' => $file_rev_id,
			   			 'file_review_id' => $file_review_id
			   		)
			   );
			   
		$logger->addLog($sql);
	}	
}