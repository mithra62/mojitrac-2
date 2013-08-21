<?php
class PM_Model_Options_Companies
{
	static public function types()
	{
		$types = array();
		$types[0] = 'Not Applicable';
		$types[1] = 'Client';
		$types[2] = 'Vendor';
		$types[3] = 'Supplier';
		$types[4] = 'Consultant';
		$types[5] = 'Government';
		$types[6] = 'Internal';
		
		return $types;
	}
	
	static public function translateTypeId($id)
	{
		$types = PM_Model_Options_Companies::types();
		return $types[$id];
	}
	
	static public function companies($blank = FALSE, $empty = FALSE, $types = FALSE, $ids = FALSE)
	{
		if($empty)
		{
			return array();
		}
		
		$companies = new PM_Model_Companies(new PM_Model_DbTable_Companies);
		$arr = $companies->getAllCompanyNames($types, $ids);
		$_new = array();
		if($blank)
		{
			$_new[null] = '';
		}
		foreach($arr AS $company)
		{
			$_new[$company['id']] = $company['name'];
		}
		
		return $_new;
	}
}