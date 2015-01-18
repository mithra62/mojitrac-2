<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Cron/Archive.php
 */

namespace HostManager\Cron;

use Base\Cron\BaseCron;

class Archive extends BaseCron
{
	public function shouldRun()
	{
	
	}
	
	public function run()
	{
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$task->autoArchive($days, $status);
	}	
}