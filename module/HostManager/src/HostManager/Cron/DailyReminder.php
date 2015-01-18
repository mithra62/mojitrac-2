<?php
namespace HostManager\Cron;

use Base\Cron\BaseCron;

class DailyReminder extends BaseCron
{
	public function shouldRun()
	{
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
	}
	
	public function run()
	{
		
	}
}