<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/GlobalAlerts.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;

 /**
 * PM - Global Alerts View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View/Helper/GlobalAlerts.php
 */
class GlobalAlerts extends AbstractHelper
{   	
	function __invoke($id)
	{
		if(!$id)
		{
			return FALSE;
		}
		
		$return = '';
		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$overdue_tasks = $user->userHasOverdueTasks($id);
		if($overdue_tasks && is_array($overdue_tasks) && $overdue_tasks['total_count'] >= 1)
		{
			$return .= '<div class="global-alert global-fail"><div>You have <a href="'.$this->view->url(array('module' => 'pm','controller'=>'index','action'=>'index'), null, TRUE).'" style="text-decoration:none; color: #CC3300">'.$overdue_tasks['total_count'].' overdue tasks</a>.</div></div>';
		}
		
		$prefs = Zend_Registry::get('pm_prefs');
		if(isset($prefs['timer_data']) && $prefs['timer_data'] != '')
		{
			$timer = new PM_Model_Timers;
			$data = $timer->decodeTimerData($prefs['timer_data']);
			if(is_array($data))
			{
				$return .= '<div class="global-alert global-information"><div class="timer-alert"> <a href="'.$this->view->url(array('module' => 'pm','controller'=>'timers','action'=>'stop'), null, TRUE).'" rel="facebox" style="text-decoration:none; color: #0033FF">Timer running for '.$data['name'].': <span id="timer_countdown"></span></a></div></div>';
				$return .= "<script>$('#timer_countdown').countdown({since: new Date('".$timer->makeCountdownDate($data['start_time'])."'), compact: true, format: 'yowdhmS', description: ''});</script>";					
			}
		}
		
		return $return;
		//return $return;
	}
}