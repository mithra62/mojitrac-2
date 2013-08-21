<?php
class PM_Model_Options_Tasks extends PM_Model_Options_Abstract
{
	static public function types()
	{
		$options = new PM_Model_Options(new PM_Model_DbTable_Options);
		return parent::filterOptions($options->getAllTaskTypes());
	}
	
	static public function access()
	{
		$access = array();
		$access['1'] = 'Public';
		$access['2'] = 'Protected';
		$access['3'] = 'Participant';
		$access['4'] = 'Private';
		
		return $access;
	}
	
	static public function tasks()
	{
		return array();
	}	
	
	static public function translateTypeId($id)
	{
		$types = self::types();
		if(isset($types[$id]))
		{
			return $types[$id];
		}
		else
		{
			return $types[0];
		}
	}
	
	static public function progress()
	{
		$arr = array();
		
		$i = 0;
		while($i <= 100)
		{
			$arr[$i] = $i;
			$i = ($i+5);
			if($i > 100)
				break;
		}
		return $arr;
	}
}