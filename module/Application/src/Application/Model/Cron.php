<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Cron.php
 */

namespace Application\Model;

use Cron\CronExpression;

/**
 * Cron Model
 *
 * @package 	Cron
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Application/src/Application/Model/Cron.php
*/
class Cron extends AbstractModel
{
	/**
	 * The path to where Cron objects are stored
	 * @var string
	 */
	private $path = null;
	
	/**
	 * Sets everything up
	 * @ignore
	 * @param \Zend\Db\Adapter\Adapter $db
	 * @param \Zend\Db\Sql\Sql $sql
	 * @param \Application\Model\Users $users
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $db, \Zend\Db\Sql\Sql $sql)
	{
		parent::__construct($db, $sql);
	}
	
	/**
	 * Sets the directory where Cron scripts are stored
	 * @param string $path
	 * @return \Application\Model\Cron
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}
	
	/**
	 * Executes all the configured Crons 
	 * @return boolean
	 */
	public function run(\Zend\Console\Adapter\AbstractAdapter $cron)
	{
		$cron = CronExpression::factory('@daily');
		$cron->isDue();
		echo $cron->getNextRunDate()->format('Y-m-d H:i:s');
		return true;
	}
}