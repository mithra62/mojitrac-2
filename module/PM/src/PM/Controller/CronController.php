<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/CronController.php
*/

/**
 * PM - Cron Controller
 *
 * Routes the Cron requests
 *
 * @package 		mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/modules/pm/controllers/CronController.php
 */
class PM_CronController extends Zend_Controller_Action
{
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
		$this->session = new Zend_Session_Namespace('PM');
    	$this->view->layout()->disableLayout();
    	$this->view->ajax_mode = TRUE;	
    	$this->_helper->ViewRenderer->setNoRender(true);	
	}
	
	public function dailyTaskReminderAction()
	{
		
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
	
	public function autoArchiveTasksAction()
	{
		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		echo $task->autoArchive();
		exit;
	}
}