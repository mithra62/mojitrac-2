<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Tasks.php
 */

namespace PM\Model\Options;

/**
 * PM - Tasks Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Options/Tasks.php
 */
class Tasks extends AbstractOptions
{
	static public function types()
	{		
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
	
	static public function translateTypeId($id, \PM\Model\Options $options)
	{
		$types = $options->getAllTaskTypes();
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