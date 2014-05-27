<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Sql/UserRolePermissions.php
 */

namespace HostManager\Model\Sql;

use HostManager\Model\Sql\SqlAbstract;

/**
 * HostManager - user_role_permissions table class
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/src/HostManager/Model/Sql/UserRolePermissions.php
 */
class UserRolePermissions extends SqlAbstract {
	
	/**
	 * Appends the account_id column to all SELECT calls to filter database queries
	 * @param \Zend\Db\Sql\Select $sql
	 * @return \Zend\Db\Sql\Select
	 */
	public function Select(\Zend\Db\Sql\Select $sql, $account_id)
	{
		return parent::Select($sql, $account_id);
	}
}