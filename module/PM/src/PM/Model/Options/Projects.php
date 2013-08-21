<?php
class PM_Model_Options_Projects extends PM_Model_Options_Abstract
{
	static public function types()
	{
		$options = new PM_Model_Options(new PM_Model_DbTable_Options);
		return parent::filterOptions($options->getAllProjectTypes());
	}
	
	static public function translatePriorityId($id)
	{
		$priority = PM_Model_Options_Projects::priorities();
		return $priority[$id];		
	}
	
	static public function translateTypeId($id)
	{
		$types = PM_Model_Options_Projects::types();
		if(!isset($types[$id]))
		{
			$id = 0;
		}
		return $types[$id];
	}
	
	static public function translateStatusId($id)
	{
		$status = PM_Model_Options_Projects::status();
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