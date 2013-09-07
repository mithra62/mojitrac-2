<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Companies.php
 */

namespace PM\Model\Options;

/**
 * PM - Companies Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Options/Companies.php
 */
class Companies extends AbstractOptions
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
		$types = self::types();
		return $types[$id];
	}
	
	static public function companies(\PM\Model\Companies $companies, $blank = FALSE, $empty = FALSE, $types = FALSE, $ids = FALSE)
	{
		if($empty)
		{
			return array();
		}
		
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