<?php
class Zend_View_Helper_RelativeDate
{
	function RelativeDate($date)
	{
		$_date = new LambLib_Controller_Action_Helper_Utilities;
		return $_date->relative_datetime($date); 
	}
}