<?php
class Zend_View_Helper_ProjectStatus
{
	function ProjectStatus($status)
	{
		return PM_Model_Options_Projects::translateStatusId($status); 
	}
}