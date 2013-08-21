<?php
class Zend_View_Helper_FileStatus
{
	function FileStatus($status)
	{
		return PM_Model_Options_Files::translateStatusId($status); 
	}
}