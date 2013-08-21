<?php
class Zend_View_Helper_TaskType
{
	function TaskType($type)
	{
		return PM_Model_Options_Tasks::translateTypeId($type); 
	}
}