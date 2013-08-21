<?php
class PM_Model_Options_Project_Team
{	
	static public function team($project_id, $blank = FALSE)
	{
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$arr = $project->getProjectTeamMembers($project_id);
		$_new = array();
		if($blank)
		{
			$_new['0'] = 'Unassigned';
		}
		foreach($arr AS $user)
		{
			$_new[$user['user_id']] = $user['first_name'].' '.$user['last_name'];
		}
		
		return $_new;
	}
}