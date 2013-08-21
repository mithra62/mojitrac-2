<?php
class PM_Model_Options_Notes
{
	static public function topics()
	{
		$topics = array();
		$topics[null] = '';
		$topics[1] = 'Phone Call';
		$topics[2] = 'Conference Notes';
		$topics[3] = 'Email Message';
		$topics[4] = 'General Notes';
		
		return $topics;
	}
	
	static public function translateTopicId($id)
	{
		$topics = self::topics();
		return $topics[$id];
	}
	
	static public function companies($blank = FALSE, $empty = FALSE)
	{
		if($empty)
		{
			return array();
		}
		
		$companies = new PM_Model_Companies(new PM_Model_DbTable_Companies);
		$arr = $companies->getAllCompanyNames();
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