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
		return $member_id;
		exit;
		
		
		$users = new PM_Model_Users(new PM_Model_DbTable_Users);
		$user_data = $users->getAllUsers('d');
		foreach($user_data AS $user)
		{
			if($users->checkPreference($user['id'], 'noti_daily_task_reminder', '1') == '0')
			{
				continue;
			}
			
			$users_tasks = $users->getAssignedTasks($user['id'], 30);
			if(count($users_tasks) >= 1)
			{	
				$noti = new PM_Model_Notifications;
				$noti->sendDailyTaskReminder($user, $users_tasks);
			}
		}
		exit;
	}
}
