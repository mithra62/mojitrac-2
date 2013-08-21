<?php
class Zend_View_Helper_ProjectType
{
	function ProjectType($type)
	{
		return PM_Model_Options_Projects::translateTypeId($type); 
	}
}