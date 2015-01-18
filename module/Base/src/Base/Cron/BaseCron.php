<?php
namespace Base\Cron;

use Cron\CronExpression;

class BaseCron
{
	public function setRunDate()
	{
		
	}
	
	public function isDue()
	{
		$cron = CronExpression::factory('@daily');
		$cron->isDue();
		echo $cron->getNextRunDate()->format('Y-m-d H:i:s');		
	}
}