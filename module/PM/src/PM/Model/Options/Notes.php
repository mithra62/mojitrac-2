<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Notes.php
 */

namespace PM\Model\Options;

/**
 * PM - Projects Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Options/Notes.php
 */
class Notes
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