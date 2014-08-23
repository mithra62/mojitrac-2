<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/CliController.php
 */

namespace PM\Controller;

use Application\Controller\AbstractController;

/**
 * PM - Command Line Controller
 *
 * Handles the PM module Console requests
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Controller/CliController.php
 */
class CliController extends AbstractController
{
	/**
	 * Console command to archive tasks 
	 * @return string
	 */
    public function archiveTasksAction()
    {
    	$days = $this->params()->fromRoute('days', 7);
    	$status = $this->params()->fromRoute('status', 6);
    	$verbose = $this->params()->fromRoute('verbose');
    	
    	$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    	$return = $task->autoArchive($days, $status);  
    	if($verbose)
    	{
    		return $return;
    	}
    } 

    /**
     * Sends the Daily Task Reminder email
     * @return string
     */
	public function dailyTaskReminderAction()
	{
    	$member_id = $this->params()->fromRoute('member_id');
    	$email = $this->params()->fromRoute('email');
    	$verbose = $this->params()->fromRoute('verbose');
    	
    	return $this->sendTaskReminder($member_id, $email, $verbose);
	}
	
	public function sendTaskReminder($member_id = FALSE, $email = FALSE, $verbose = FALSE)
	{
		$user = $this->getServiceLocator()->get('PM\Model\Users');
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		 
		if($member_id || $email)
		{
			$user_data = ($member_id ? $user->getUserById($member_id) : $user->getUserByEmail($email));
			if(!$user_data)
			{
				return 'User not found';
			}
		
			$user_data = array($user_data);
		}
		else
		{
			$user_data = $user->getAllUsers('d');
		}

		foreach($user_data AS $member)
		{
			if($user->checkPreference($member['id'], 'noti_daily_task_reminder', '1') == '0')
			{
				continue;
			}
		
			$user_tasks = $user->getAssignedTasks($member['id'], 30);
			if( !$user_tasks )
			{
				continue;
			}
			
			$mail = $this->getServiceLocator()->get('Application\Model\Mail');
			$mail->setTranslationDomain('pm');
			$this->email_view_path = $mail->getModulePath(__DIR__).'/view/emails';
			$mail->setTo($member['email'], $member['first_name'].' '.$member['last_name']);
			$mail->setViewDir($this->email_view_path);
			$mail->setEmailView('task-reminder', array('user_data' => $member, 'tasks' => $user_tasks));
			$mail->setSubject('daily_task_reminder_email_subject');
			$mail->send();
		}
		
		return 'done';
	}
}
