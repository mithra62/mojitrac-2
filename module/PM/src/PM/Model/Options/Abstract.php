<?php
abstract class PM_Model_Options_Abstract
{

	static public function filterOptions($options)
	{
		$arr = array();
		$arr['0'] = 'Unknown';
		foreach($options AS $option)
		{
			$arr[$option['id']] = $option['name'];
		}
		return $arr;
	}
	
	static public function priorities()
	{
		$priorities = array();
		$priorities[0] = 'None';
		$priorities[1] = 'Very Low';
		$priorities[2] = 'Low';
		$priorities[3] = 'Medium';
		$priorities[4] = 'High';
		$priorities[5] = 'Very High';
		return $priorities;		
	}
	
	static public function status()
	{
		$status = array();
		$status[0] = 'Not Defined';
		$status[1] = 'Proposed';
		$status[2] = 'In Planning';
		$status[3] = 'In Progress';
		$status[4] = 'On Hold';
		$status[5] = 'Complete';
		$status[6] = 'Archived';
		return $status;
	}	
}