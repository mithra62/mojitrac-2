<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Projects.php
 */

namespace PM\Model\Options;

/**
 * PM - Projects Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Options/Projects.php
 */
class Projects extends AbstractOptions
{
	static public function types()
	{
		$sm = self::getServiceLocator();
		$options = $sm->get('PM\Model\Options');
		return parent::filterOptions($options->getAllProjectTypes());
	}
	
	static public function translatePriorityId($id)
	{
		$priority = self::priorities();
		return $priority[$id];		
	}
	
	static public function translateTypeId($id, \PM\Model\Options $options)
	{
		$types = $options->getAllProjectTypes();
		foreach($types AS $key => $value)
		{
			if($value['id'] == $id)
			{
				return $value['name'];
			}
		}
		
		return $types[$id];
	}
	
	static public function translateStatusId($id)
	{
		$status = self::status();
		return $status[$id];		
	}
	
	static public function projects($blank = FALSE, $company_id = FALSE)
	{
		$projects = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$arr = $projects->getProjectOptions($company_id);
		
		$_new = array();
		if($blank)
		{
			$_new[null] = '';
		}
		foreach($arr AS $project)
		{
			$_new[$project['id']] = $project['name'];
		}
		return $_new;
	}
}