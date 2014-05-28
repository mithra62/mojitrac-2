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
	public function Select(\Zend\Db\Sql\Select $sql, $account_id)
	{
		$raw_state = $sql->getRawState();
		$table = $this->getTableName($raw_state['table']);
		$sql->where(array($table.'.account_id' => $account_id));
		return $sql;
	}
		
	/**
	 * Handles the SELECT statements for filtering by Account
	 * @param \Zend\Db\Sql\Insert $sql
	 */
	public function Insert(\Zend\Db\Sql\Insert $sql, $account_id)
	{
		$sql->values(array('account_id' => $account_id), $sql::VALUES_MERGE);
		return $sql;
	}
	
	/**
	 * Returns the name of the table we're working
	 *
	 * Parses the output from getRawState() to determine which table we're working with.
	 *
	 * @param mixed $table
	 * @return string
	 */
	public function getTableName($table)
	{
		//we're dealing with an alias so parse it for its parts
		if( is_array($table) )
		{
			$string = '';
			foreach($table AS $key => $value)
			{
				$string = $key;
			}
	
			$table = $string;
		}
		
		return $table;
	}	

}