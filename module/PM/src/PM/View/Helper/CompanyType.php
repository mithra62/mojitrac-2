<?php
class Zend_View_Helper_CompanyType
{
	function CompanyType($type)
	{
		return PM_Model_Options_Companies::translateTypeId($type); 
	}
}