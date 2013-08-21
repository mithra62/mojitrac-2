<?php
class Zend_View_Helper_IsDatePast
{
	public function IsDatePast($date)
	{
		$d = strtotime($date);
		if($d && $d < time())
		{
			return TRUE;
		}
	}
}