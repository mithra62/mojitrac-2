<?php
class PM_Model_Options_Datetime
{
	static public function minutes()
	{
		$arr = array();
		$i = 0;
		$arr['hour'] = 'Minutes';
		while($i <= 60)
		{
			$arr[$i] = $i;
			$i++;
		}
		return $arr;
	}
	
	static public function hours()
	{
		$arr = array();
		$i = 0;
		$arr['hour'] = 'Hour';
		$_tail = ' AM';
		while($i <= 24)
		{
			$hour = ($i == '0' ? '12' : $i);
			if($i >= 12)
			{
				$_tail = ' PM';
			}
			
			if($i > 12)
			{
				$hour = ($i-12);
			}
			
			$arr[$i] = $hour.$_tail;
			$i++;
		}
		return $arr;
	}
	
	static public function date_formats()
	{
		$arr = array();
		$arr['Y/m/d'] = date('Y/m/d');
		$arr['m/d/Y'] = date('m/d/Y');
		$arr['d/m/Y'] = date('d/m/Y');
		$arr['F j, Y'] = date('F j, Y');
		$arr['custom'] = "Custom";
		return $arr;
	}
	
	static public function time_formats()
	{
		$arr = array();
		$arr['g:i a'] = date('g:i a');
		$arr['g:i A'] = date('g:i A');
		$arr['H:i'] = date('H:i');
		$arr['custom'] = "Custom";
		return $arr;
	}	
}