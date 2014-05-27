<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Sql/SqlAbstract.php
 */

namespace HostManager\Model\Sql;

/**
 * HostManager - SqlAbstract
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/src/HostManager/Model/Sql/SqlAbstract.php
 */
abstract class SqlAbstract {
	
	/**
	 * Handles the SELECT statements for filtering by Account
	 * @param \Zend\Db\Sql\Select $sql
	 */
	public abstract function Select(\Zend\Db\Sql\Select $sql, $account_id);

}