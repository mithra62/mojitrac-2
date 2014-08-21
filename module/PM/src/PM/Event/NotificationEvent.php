<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Event/NotificationEvent.php
 */

namespace PM\Event;

use Base\Event\BaseEvent;
use Application\Model\Mail;
use PM\Model\Users;
use PM\Model\Projects;
use PM\Model\Tasks;

 /**
 * PM - Notification Events
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Event/NotificationEvent.php
 */
class NotificationEvent extends BaseEvent
{
    /**
     * User Identity
     * @var int
     */
    public $identity = false;
    
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'user.add.post' => 'sendUserAdd',
    	'task.update.pre' => 'sendTaskUpdate',
    	'task.assign.pre' => 'sendTaskAssign'
    );
    
    /**
     * The Notification Event
     * @param \Application\Model\Mail $mail
     * @param \Application\Model\Users $users
     * @param string $identity
     */
    public function __construct( Mail $mail, Users $users, Projects $project, Tasks $task, $identity = null)
    {
        $this->mail = $mail;
        $this->identity = $identity;
        $this->user = $users;
        $this->project = $project;
        $this->task = $task;
        
        $this->email_view_path = $this->mail->getModulePath(__DIR__).'/view/emails';
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
     * Sends the user registration notification
     * @param \Zend\EventManager\Event $event
     */
    public function sendUserAdd(\Zend\EventManager\Event $event)
    {
    	$data = $event->getParam('data');
    	$user_id = $event->getParam('user_id');
    	$this->mail->addTo($data['email'], $data['first_name'].' '.$data['last_name']);
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('user-registration', array('user_data' => $data, 'user_id' => $user_id));
    	$this->mail->setSubject('user_registration_email_subject');
    	$this->mail->send($mail->transport);    	
    }
    

    /**
     * Sends the Task Status Change email notifications
     * @param int $task_id
     * @param array $new_data
     * @param array $old_data
     */
    public function sendTaskStatusChange($task_id, array $new_data, array $old_data)
    {
    	$team = $this->project->getProjectTeamMembers($new_data['project_id']);
    	$project_data = $this->project->getProjectById($new_data['project_id']);
    	$sending = FALSE;
    	foreach($team AS $member)
    	{
    		if($this->user->checkPreference($member['user_id'], 'noti_status_task', '1') == '0')
    		{
    			continue;
    		}
    		
    		$sending = TRUE;
    		$this->mail->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
    	}   

    	if( !$sending )
    	{
    		return; //no emails were added to send to so bounce out
    	}
    	
    	$view_data = array(
    		'task_data' => $new_data, 
    		'task_id' => $task_id, 
    		'project_data' => $project_data
    	);
    	
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('task-status-change', $view_data);
    	$this->mail->setSubject($this->mail->translator->translate('email_subject_task_status_change', 'pm').': '.$new_data['name']);
    	$this->mail->send($mail->transport);
    }

    /**
     * Sends the Task Priority Change email notifications
     * @param int $task_id
     * @param array $new_data
     * @param array $old_data
     */
    public function sendTaskPriorityChange($task_id, array $new_data, array $old_data)
    {
    	$team = $this->project->getProjectTeamMembers($new_data['project_id']);
    	$project_data = $this->project->getProjectById($new_data['project_id']);
    	$sending = FALSE;
    	foreach($team AS $member)
    	{
    		if($this->user->checkPreference($member['user_id'], 'noti_priority_task', '1') == '0')
    		{
    			continue;
    		}
    
    		$sending = TRUE;
    		$this->mail->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
    	}
    
    	if( !$sending )
    	{
    		return; //no emails were added to send to so bounce out
    	}
    	 
    	$view_data = array(
    		'task_data' => $new_data,
    		'task_id' => $task_id,
    		'project_data' => $project_data
    	);
    	 
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('task-priority-change', $view_data);
    	$this->mail->setSubject($this->mail->translator->translate('email_subject_task_priority_change', 'pm').': '.$new_data['name']);
    	$this->mail->send($mail->transport);
    } 

    /**
     * Sends the Task End Date Update email notifications
     * @param int $task_id
     * @param array $new_data
     * @param array $old_data
     */
    public function sendTaskEndDateChange($task_id, array $new_data, array $old_data)
    {
    	$team = $this->project->getProjectTeamMembers($new_data['project_id']);
    	$project_data = $this->project->getProjectById($new_data['project_id']);
    	$sending = FALSE;
    	foreach($team AS $member)
    	{
    		if($this->user->checkPreference($member['user_id'], 'noti_priority_task', '1') == '0')
    		{
    			continue;
    		}
    
    		$sending = TRUE;
    		$this->mail->addTo($member['email'], $member['first_name'].' '.$member['last_name']);
    	}
    
    	if( !$sending )
    	{
    		return; //no emails were added to send to so bounce out
    	}
    
    	$view_data = array(
    		'task_data' => $new_data,
    		'task_id' => $task_id,
    		'project_data' => $project_data
    	);
    
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('task-priority-change', $view_data);
    	$this->mail->setSubject($this->mail->translator->translate('email_subject_task_priority_change', 'pm').': '.$new_data['name']);
    	$this->mail->send($mail->transport);
    }    
    
    /**
     * Sends the emails for when a task is modified
     * @param \Zend\EventManager\Event $event
     */
    public function sendTaskUpdate(\Zend\EventManager\Event $event)
    {
    	$task_id = $event->getParam('task_id');
    	$new_data = $event->getParam('data');
    	$task_data = $this->task->getTaskById($task_id);
    	if($new_data['status'] != $task_data['status'] && ($new_data['priority'] == $task_data['priority']))
    	{
    		$this->sendTaskStatusChange($task_id, $new_data, $task_data);
    	}
    	else if($new_data['priority'] != $task_data['priority'])
    	{
    		$this->sendTaskPriorityChange($task_id, $new_data, $task_data);
    	}
    	
    	if($new_data['end_date'] != $task_data['end_date'])
    	{
    		//$noti = new PM_Model_Notifications;
    		//$noti->sendTaskEndDateChange($task_data);
    		//echo "sendTaskEndDateChange";
    		//exit;
    	}    	
    }
    
    /**
     * Sends the email when a task is assigned to someone
     * @param \Zend\EventManager\Event $event
     */
    public function sendTaskAssign(\Zend\EventManager\Event $event)
    {
    	$task_id = $event->getParam('task_id');
    	$assigned_to = $event->getParam('assigned_to');
    	if($this->user->checkPreference($assigned_to, 'noti_assigned_task', '1') != '0')
    	{
    		$user_data = $this->user->getUserById($assigned_to);
    		$task_data = $this->task->getTaskById($task_id);
    		$project_data = $this->project->getProjectById($task_data['project_id']);
    		$this->mail->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
    		$this->mail->setViewDir($this->email_view_path);
    		
    		$view_data = array(
    			'assigned_to' => $assigned_to,
    			'task_id' => $task_id,
    			'user_data' => $user_data,
    			'task_data' => $task_data,
    			'project_data' => $project_data
    		);
    		    		
    		$this->mail->setEmailView('task-assigned', $view_data);
    		$this->mail->setSubject($this->mail->translator->translate('email_subject_task_assigned', 'pm').': '.$task_data['name']);
    		$this->mail->send($mail->transport);    		
    	}
    }
}