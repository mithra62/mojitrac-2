<?php
class Zend_View_Helper_CheckPermission Extends PM_Model_Permissions
{
	function CheckPermission($permission)
	{
		$identity = Zend_Auth::getInstance()->getIdentity();
		return $this->check($identity, $permission);
	}
}