<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Event/ActivityLogEvent.php
 */

namespace PM\Event;

use Base\Event\BaseEvent;

 /**
 * PM - Event Activity Log
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Event/ActivityLogEvent.php
 */
class ActivityLogEvent extends BaseEvent
{	
    /**
     * Event Manager Model
     * @var \PM\Model\ActivityLog
     */
    public $al = false;
    
    /**
     * User Identity
     * @var int
     */
    public $identity = false;
    
    /**
     * The project data that's to be removed
     * @var array
     */
    private $removed_project_data = array();
    
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'project.update.pre' => 'logProjectUpdate',
        'project.add.post' => 'logProjectAdd',
        'project.addteam.post' => 'logProjectTeamAdd',
    	'project.remove.pre' => 'prepLogProjectRemove',
    	'project.remove.post' => 'logProjectRemove'
    );
    
    /**
     * Activity Log Event
     * @param \PM\Model\ActivityLog $al
     * @param int $identity
     */
    public function __construct( \PM\Model\ActivityLog $al, $identity)
    {
        $this->al = $al;
        $this->identity = $identity;
    }
    
    /**
     * Registers the Event with ZF and our Application Model
     * @param \Zend\EventManager\SharedEventManager $ev
     */
    public function register( \Zend\EventManager\SharedEventManager $ev)
    {
        foreach($this->hooks AS $key => $value)
        {
        	$ev->attach('Base\Model\BaseModel', $key, array($this, $value));
        }        
    }
    
	/**
	 * Wrapper to log a task add entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logTaskAdd(array $data, $task_id, $project_id, $performed_by)
	{
		$this->al->logActivity(self::setDate(), 'task_add', $performed_by, $data, $project_id, 0, $task_id);
	}
	
	/**
	 * Wrapper to log a task update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logTaskUpdate(array $data, $task_id, $project_id, $performed_by)
	{
		$this->al->logActivity(self::setDate(), 'task_update', $performed_by, $data, $project_id, 0, $task_id);
	}	
	
	/**
	 * Wrapper to log a task assignment entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logTaskAssignment(array $data, $task_id, $project_id, $performed_by)
	{
		$this->al->logActivity(self::setDate(), 'task_assigned', $performed_by, $data, $project_id, 0, $task_id, 0, 0, $data['assigned_to']);
	}
	
	/**
	 * Wrapper to log a project removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logTaskRemove(array $data, $task_id, $project_id, $performed_by)
	{
		$this->al->logActivity(self::setDate(), 'task_remove', $performed_by, $data, $project_id, 0, $task_id);
	}	
	
	/**
	 * Wrapper to log a project update entry
	 * @todo Check for existance of a project add log before creating a new one
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function logProjectAdd(\Zend\EventManager\Event $event)
	{
		$data = $event->getParam('data');
		$project_id = $event->getParam('project_id');
		$company_id = $data['company_id'];
		$data = array('stuff' => $data, 'project_id' => $project_id, 'company_id' => $company_id, 'type' => 'project_add', 'performed_by' => $this->identity);
		$this->al->logActivity($data);		
	}
	
	/**
	 * Wrapper to log a project update entry
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function logProjectUpdate(\Zend\EventManager\Event $event)
	{
	    $data = $event->getParam('data');
	    $project_id = $event->getParam('project_id');
	    $data = array('stuff' => $data, 'project_id' => $project_id, 'type' => 'project_update', 'performed_by' => $this->identity);    
		$this->al->logActivity($data);
	}
	
	/**
	 * Prepares the project data for use by the logger after the project is actually removed
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function prepLogProjectRemove(\Zend\EventManager\Event $event)
	{
	    $project_id = $event->getParam('id');
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$this->removed_project_data = $project->getProjectById($project_id);
	}
	
	/**
	 * Wrapper to log a project removal
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function logProjectRemove(\Zend\EventManager\Event $event)
	{
	    $project_id = $event->getParam('id');
	    $project = $this->getServiceLocator()->get('PM\Model\Projects');
	    $data = (!empty($this->removed_project_data) ? $this->removed_project_data : '');
	    $data = array('stuff' => $data, 'project_id' => $project_id, 'type' => 'project_remove', 'performed_by' => $this->identity);    
		$this->al->logActivity($data);
	}
	
	/**
	 * Wrapper to log a project removal
	 * @param \Zend\EventManager\Event $event
	 * @return void
	 */
	public function logProjectTeamRemove(array $data, $id, $performed_by)
	{
		$this->al->logActivity(self::setDate(), 'project_team_remove', $performed_by, $data, $id);
	}

	/**
	 * Wrapper to log a project removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logProjectTeamAdd(\Zend\EventManager\Event $event)
	{
		$user_id = $event->getParam('id');
		$project_id = $event->getParam('project');
		$data = array('user_id' => $user_id, 'project_id' => $project_id, 'type' => 'project_team_add', 'performed_by' => $this->identity);
		$this->al->logActivity($data);
	}
	
	/**
	 * Wrapper to log a note update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logNoteAdd(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'note_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], $id);
	}
	
	/**
	 * Wrapper to log a note update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logNoteUpdate(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'note_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], $id);
	}
	
	/**
	 * Wrapper to log a note removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logNoteRemove(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'note_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], $id);
	}
	
	/**
	 * Wrapper to log a bookmark update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logBookmarkAdd(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'bookmark_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, $id);
	}
	
	/**
	 * Wrapper to log a bookmark update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logBookmarkUpdate(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'bookmark_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, $id);
	}
	
	/**
	 * Wrapper to log a bookmark removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logBookmarkRemove(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'bookmark_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, $id);
	}

	/**
	 * Wrapper to log a file entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileAdd(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'],0, 0, 0, $id);
	}
	
	/**
	 * Wrapper to log a file update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileUpdate(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $id);
	}
	
	/**
	 * Wrapper to log a file removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileRemove(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $id);
	}
	
	/**
	 * Wrapper to log a file revision entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileRevisionAdd(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_revision_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'],0, 0, 0, $data['file_id'], $id);
	}
	
	/**
	 * Wrapper to log a file revision update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileRevisionUpdate(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_revision_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $data['file_id'], $id);
	}
	
	/**
	 * Wrapper to log a file revision removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileRevisionRemove(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_revision_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $data['file_id'], $id);
	}

	/**
	 * Wrapper to log a file revision entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileReviewAdd(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_review_add', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'],0, 0, 0, $data['file_id'], $data['revision_id'], $id);
	}
	
	/**
	 * Wrapper to log a file revision update entry
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileReviewUpdate(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_review_update', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $data['file_id'], $data['revision_id'], $id);
	}
	
	/**
	 * Wrapper to log a file revision removal
	 * @param array $data
	 * @param int $id
	 * @param int $performed_by
	 * @return void
	 */
	public function logFileReviewRemove(array $data, $id, $performed_by)
	{
		$data = $this->filterForKeys($data);
		$this->al->logActivity(self::setDate(), 'file_review_remove', $performed_by, $data, $data['project_id'], $data['company_id'], $data['task_id'], 0, 0, 0, $data['file_id'], $data['revision_id'], $id);
	}	
	
	/**
	 * Takes the array and verifies the existance of the primary keys
	 * @param $data
	 */
	private function filterForKeys(array $data)
	{
		$data['company_id'] = $data['project_id'] = $data['task_id'] = 0;
		if(array_key_exists('company', $data))
		{
			$data['company_id'] = $data['company'];
		}
	
		if(array_key_exists('project', $data))
		{
			$data['project_id'] = $data['project'];
		}
	
		if(array_key_exists('task', $data))
		{
			$data['task_id'] = $data['task'];
		}
	
		return $data;
	}	
}