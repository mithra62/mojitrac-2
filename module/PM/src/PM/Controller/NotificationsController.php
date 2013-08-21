<?php
class PM_NotificationsController extends Zend_Controller_Action
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
			$users_tasks = $users->getAssignedTasks($user['id'], 30);
			if(count($users_tasks) >= 1)
			{
				$noti = new PM_Model_Notifications;
				$noti->sendDailyTaskReminder($user, $users_tasks);
			}
		}
	}
}