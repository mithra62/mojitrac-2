<?php
class Zend_View_Helper_TaskStatus
{
	function TaskStatus($status)
	{
		return PM_Model_Options_Projects::translateStatusId($status); 
	}
}