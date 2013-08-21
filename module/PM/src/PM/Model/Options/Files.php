<?php
class PM_Model_Options_Files
{
	static public function status()
	{
		$types = array();
		$types[0] = 'Not Approved';
		$types[1] = 'Approved With Changes';		
		$types[2] = 'Needs Approval';
		$types[3] = 'No Approvals Needed';
		$types[4] = 'Approved';
		
		return $types;
	}

	static public function translateStatusId($id)
	{
		$types = self::status();
		return $types[$id];
	}
}